<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
 * institutional repositories in their Omeka repositories.
 *
 * The FedoraConnector plugin provides methods to generate calls against Fedora-
 * based content disemminators. Unlike traditional ingestion techniques, this
 * plugin provides a facade to Fedora-Commons repositories and records pointers
 * to the "real" objects rather than creating new physical copies. This will
 * help ensure longer-term durability of the content streams, as well as allow
 * you to pull from multiple institutions with open Fedora-Commons
 * respositories.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      Ethan Gruber <ewg4x@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      Wayne Graham <wayne.graham@virginia.edu>
 * @author      Eric Rochester <err8n@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

require "Zend/Form/Element.php";
include_once '../form_utils.php';
include_once '../db_utils.php';

/**
 * This class defines actions for datastream items.
 */
class FedoraConnector_DatastreamsController extends Omeka_Controller_Action
{

    /**
     * This controls creating the main page for the datastream item.
     *
     * @return void
     */
    public function indexAction(){
        $item_id = $this->_getParam('id');
        $form = $this->_getPidForm($item_id);
        $this->view->id = $item_id;
        $this->view->form = $form;
    }

    /**
     * This retrieves and renders a set of datastream records.
     *
     * @return void
     */
    public function browseAction()
    {
        $db = get_db();

        $deleteUrl = WEB_ROOT . '/admin/fedora-connector/datastreams/delete/';
        $currentPage = $this->_getParam('page', 1);
        $count = $db
            ->getTable('FedoraConnector_Datastream')
            ->count();

        $this->view->datastreams = Fedora_Db_getDataForPage(
            'FedoraConnector_Datastream',
            $currentPage
        );
        $this->view->count = $count;
        $this->view->deleteUrl = $deleteUrl;

        // Pagination.
        $baseUrl = $this
            ->getRequest()
            ->getBaseUrl();
        $paginationUrl = $baseUrl . '/datastreams/browse/';

        $pagination = array(
            'page'          => $currentPage,
            'per_page'      => 10,
            'total_results' => $count,
            'link'          => $paginationUrl
        );

        Zend_Registry::set('pagination', $pagination);
    }

    /**
     * This retrieves and renders a set of datastream
     *
     * @return void
     */
    public function selectAction()
    {
        if ($_POST) {
            $form = $this->_getPidForm();

            if ($form->isValid($this->_request->getPost())) {
                // Get posted values.
                $uploadedData = $form->getValues();
                $item_id = ($uploadedData['fedora_connector_item_id'] > 0)
                    ? $uploadedData['fedora_connector_item_id']
                    : 0;
                $pid = $uploadedData['fedora_connector_pid'];
                $server_id = $uploadedData['fedora_connector_server_id'];

                $datastreamsForm = $this->_getDatastreamsForm(
                    $item_id,
                    $pid,
                    $server_id
                );
                $this->view->form = $datastreamsForm;
            }
        } else {
            $this->flashError('Failed to gather posted data.');
        }
    }

    /**
     * This handles updating the form.
     *
     * @return void
     */
    public function updateAction()
    {
        // XXX some -> models/FedoraConnector/Datastream.php (updateFromArray)
        $form = $this->_getDatastreamsForms();

        if ($_POST) {
            $uploadedData = $this->_request->getPost();

            if ($form->isValid($uploadedData)) {
                // Get posted values.
                if ($uploadedData['fedora_connector_item_id'] > 0) {
                    $item_id = $uploadedData['fedora_connector_item_id'];
                } else {
                    $item = new Item;
                    $item->save();
                    $item_id = $item->id;
                }

                $pid = $uploadedData['fedora_connector_pid'];
                $metadataStream = $uploadedData['fedora_connector_metadata'];
                $server_id = $uploadedData['fedora_connector_server_id'];

                $posted = 0;
                foreach ($uploadedData as $k => $v) {
                    if (strpos($k, 'fedora_connector_datastream') !== false
                        && $v != '0'
                    ) {
                        $datastream = substr($k, 28); // Shave off the prefix.
                        $data = array(
                            'item_id'         => $item_id,
                            'pid'             => $pid,
                            'datastream'      => $datastream,
                            'mime_type'       => $v,
                            'metadata_stream' => $metadataStream,
                            'server_id'       => $server_id
                        );

                        if ($this->_updateDb($db, $datastream, $v, $data)) {
                            $posted += 1;
                        }

                    }
                }

                if ($posted > 0) {
                    $this->flashSuccess('Fedora datastreams connected to Item');
                    $this->_helper->redirector->goto($item_id, 'edit', 'items');
                } else {
                    $this->flashError('No datastreams selected.');
                    $this->_helper->redirector->goto($item_id, 'edit', 'items');
                }

            } else {
                // Ummm. No.
                // var_dump($this->_request->getPost());
                $this->flashError('Failed to gather posted data.');
            }
        }
    }

    /**
     * This updates the database with information from the form.
     *
     * @param Omeka_Db     $db         This is the database connection.
     * @param Omeka_Record $datastream This is the datastream type to add.
     * @param string       $value      This is the value from the form.
     * @param array        $data       This is the data structure for this 
     * item.
     *
     * @return boolean True if the information was added successfully.
     */
    private function _updateDb($db, $datastream, $value, $data)
    {
        // XXX -> models/FedoraConnector/Datastream.php
        try {
            // Update the database with new values.
            $db = get_db();
            $fedoraconnector_id = $db->insert(
                'fedora_connector_datastreams',
                $data
            );

            // If TeiDisplay is installed, write an entry to 
            // the table if datastream is 'TEI'.
            if (function_exists('tei_display_installed')
                && $datastream == 'TEI'
                && strpos($value, 'text/xml') !== false
            ) {
                $this->_addTeiDatastream(
                    $db,
                    $datastream,
                    $fedoraconnector_id
                );
            }

            return true;

        } catch (Exception $err) {
            $this->flashError($err->getMessage());
            return false;
        }
    }

    /**
     * This adds an entry to the database if the datastream is TEI XML and the 
     * TeiDisplay plugin is installed.
     *
     * This does NOT test whether this datastream is, in fact, TEI XML. That is 
     * assumed. If you call it with something else (an image, say), the results 
     * are undefined.
     *
     * When found, the items are added to the database in tei_display_configs.
     *
     * @param Omeka_Db     $db         This is the database connection.
     * @param Omeka_Record $datastream This is the datastream.
     * @param integer      $fcId       This is the Fedora connector ID.
     *
     * @return void
     */
    private function _addTeiDatastream($db, $datastream, $fcId)
    {
        // XXX -> models/FedoraConnector/Datastream.php

        $newDatastream = $db
            ->getTable('FedoraConnector_Datastream')
            ->find($fcId);
        $server = fedora_connector_get_server($newDatastream);
        $teiFile = fedora_connector_content_url($newDatastream, $server);

        // Get the TEI ID.
        $xml_doc = new DomDocument;
        $xml_doc->load($teiFile);

        $teiNode = $xml_doc->getElementsByTagName('TEI');
        $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

        foreach ($teiNode as $teiNode) {
            $p5_id = $teiNode->getAttribute('xml:id');
        }
        foreach ($tei2Node as $tei2Node) {
            $p4_id = $tei2Node->getAttribute('id');
        }

        if (isset($p5_id)) {
            $tei_id = $p5_id;
        } else if (isset($p4_id)) {
            $tei_id = $p4_id;
        } else {
            $tei_id = null;
        }

        if ($tei_id != null){
            $teiData = array(
                'item_id'              => $item_id,
                'is_fedora_datastream' => 1,
                'fedoraconnector_id'   => $fcId,
                'tei_id'               => $tei_id
            );
            $db->insert('tei_display_configs', $teiData);
        }
    }

    /**
     * This deletes an datastream and forwards to edit the item it was attached 
     * to.
     *
     * This only passes if the user is the current user.
     *
     * @return void
     */
    public function deleteAction()
    {
        if ($user = $this->getCurrentUser()) {
            $datastreamId = $this->_getParam('id');
            $datastream = get_db()
                ->getTable('FedoraConnector_Datastream')
                ->find($datastreamId);
            $item_id = $datastream->item_id;

            $datastream->delete();

            $this->flashSuccess('Fedora datastream successfully deleted.');
            $this->_helper->redirector->goto($item_id, 'edit', 'items');

        } else{
            $this->_forward('forbidden');
        }
    }

    /**
     * This imports an item's metadata.
     *
     * After successfully importing the metadata, this redirects to the item's 
     * edit page.
     *
     * @return void
     */
    public function importAction()
    {
        // XXX some -> libraries/FedoraConnector/Importer.php
        $id = $this->_getParam('id');
        $datastream = get_db()
            ->getTable('FedoraConnector_Datastream')
            ->find($id);

        echo fedora_connector_import_metadata($datastream);

        $this->flashSuccess('Metadata succesfully imported.');
        $this->_helper->redirector->goto($datastream->item_id, 'edit', 'items');
    }

    /**
     * This builds the PID form for an item.
     *
     * @param integer $item_id The database ID for an item.
     *
     * @return Zend_Form The PID form object.
     */
    private function _getPidForm($item_id)
    {
        // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
        $servers = get_db()
            ->getTable('FedoraConnector_Server')
            ->findAll();

        $form = new Zend_Form();
        $form->setAction('select');
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');

        $serverElement = new Zend_Form_Element_Select(
            'fedora_connector_server_id'
        );
        $serverElement->setLabel('Server:');
        foreach ($servers as $server) {
            $serverElement->addMultiOption($server->id, $server->name);
            if ($server->is_default > 0) {
                $is_default = $server->id;
            }
        }
        $serverElement->setValue($is_default);
        $serverElement->setRegisterInArrayValidator(false);
        // $metadataStream->setValue('MODS');
        $form->addElement($serverElement);

        // PID
        $pid = new Zend_Form_Element_Text('fedora_connector_pid');
        $pid->setLabel('PID:');
        $pid->setRequired(true);
        $form->addElement($pid);

        // Submit button
        $form->addElement('submit','submit');
        $submitElement=$form->getElement('submit');
        $submitElement->setLabel('Submit');

        // item id
        $itemId = new Zend_Form_Element_Hidden('fedora_connector_item_id');
        $itemId->setValue($item_id);
        $form->addElement($itemId);

        return $form;
    }

    /**
     * This builds the datastreams form for an item.
     *
     * @param integer $item_id The database ID for an item.
     * @param integer $pid The PID for an item.
     * @param integer $server_id The database ID for the server.
     *
     * @return Zend_Form The form object.
     */
    private function _getDatastreamsForms($item_id, $pid, $server_id)
    {
        // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
        // Get the server from the FedoraConnecter_Server table.
        $server = get_db()
            ->getTable('FedoraConnector_Server')
            ->find($server_id)
            ->url;

        // Get excluded datastreams.
        $omittedString = get_option('fedora_connector_omitted_datastreams');

        // Get the datastreams from Fedora REST.
        $datastreams = $this->_getDatastreamNodes($server, $pid);

        $form = Fedora_initForm('update', 'post', 'multipart/form-data');

        // List available datastreams to attach.
        foreach ($datastreams as $datastream) {
            // Skip datastream if in omitted list, e.g, RELS-INT, RELS-EXT, 
            // AUDIT.
            if (! $this->_isOmitted($datastream, $omittedString)) {
                $this->_addDatastreamInput($datastream, $form);
            }
        }

        $this->_addObjectMetadataSelect($datastreams, $omittedString, $form);
        Fedora_Form_addSubmit($form);
        Fedora_Form_addHidden($form, 'fedora_connector_item_id', $item_id);
        Fedora_Form_addHidden($form, 'fedora_connector_pid', $pid);
        Fedora_Form_addHidden($form, 'fedora_connector_server_id', $server_id);

        return $form;
    }

    /**
     * This takes a server URL and PID and returns the datastreams associated 
     * with it.
     *
     * @param string  $server The server's URL.
     * @param integer $pid    The item's PID.
     *
     * @return DOMNodeList The XML nodes for datastreams associated with the 
     * item.
     */
    private function _getDatastreamNodes($server, $pid)
    {
        // XXX -> xml utils
        $datastreamXmlPath = "{$server}objects/$pid/datastreams?format=xml";

        $xml_doc = new DomDocument();
        $xml_doc->load($datastreamXmlPath);
        $xpath = new DOMXPath($xml_doc);

        $datastreams = $xpath->query("//*[local-name() = 'datastream']");
        return $datastreams;
    }

    /**
     * This attaches the information from a datastream node to a form.
     *
     * @param DOMNode   $node The datastream node from the server response.
     * @param Zend_Form $form The form to add the node to.
     *
     * @return Zend_Form_Element_Checkbox The widget added to the form.
     */
    private function _addDatastreamInput($node, $form)
    {
        // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
        $dsid = $node->getAttribute('dsid');

        $input = new Zend_Form_Element_Checkbox(
            'fedora_connector_datastream_' . $dsid
        );
        $input->setLabel($dsid);
        $input->setCheckedValue($node->getAttribute('mimeType'));

        $form->addElement($input);

        return $input;
    }

    /**
     * This tests whether the node's dsid is in the omitted list.
     *
     * @param DOMNode $node The datastream node.
     * @param string $omit The list of datastream types in the omitted list.
     *
     * @return boolean True if the node should be omitted.
     */
    private function _isOmitted($node, $omit)
    {
        // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
        return (strpos($omit, $node->getAttribute('dsid')) !== false);
    }

    /**
     * This attaches a drop-down for the metadata format.
     *
     * @param DOMNodeList $nodes The datastream nodes to add options for.
     * @param string      $omit  The list of datastream types in the omitted 
     * list.
     * @param Zend_Form   $form  The form to add the select widget to.
     *
     * @return Zend_Form_Element_Select The select node added to the form.
     */
    private function _addObjectMetadataSelect($nodes, $omit, $form)
    {
        // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
        $select = new Zend_Form_Element_Select(
            'fedora_connector_metadata'
        );
        $select->setLabel('Object Metadata:');

        foreach ($nodes as $node) {
            if (strpos($node->getAttribute('mimeType'), 'text/xml') !== false
                && ! $this->_isOmitted($node, $omit)
            ) {
                $dsid = $datastream->getAttribute('dsid');
                $select->addMultiOption($dsid, $dsid);
            }
        }

        $select->setValue('DC');
        $select->setRegisterInArrayValidator(false);

        $form->addElement($select);

        return $select;
    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
