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
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

?>

<?php

// require "Zend/Form/Element.php";
// include_once '../form_utils.php';
// include_once '../db_utils.php';

class FedoraConnector_DatastreamsController extends Omeka_Controller_Action
{

    /**
     * Redirect by default to browse action.
     *
     * @return void
     */
    public function indexAction()
    {

        // Ping to browse by default.
        $this->_forward('browse', 'datastreams', 'fedora-connector');

    }

    /**
     * Show datastreams.
     *
     * @return void
     */
    public function browseAction()
    {

        $sort_field = $this->_request->getParam('sort_field');
        $sort_dir = $this->_request->getParam('sort_dir');

        // Get the datastreams.
        $page = $this->_request->page;
        $order = fedorahelpers_doColumnSortProcessing($sort_field, $sort_dir);
        $datastreams = $this->getTable('FedoraConnectorDatastream')->getDatastreams($page, $order);

        $this->view->datastreams = $datastreams;
        $this->view->current_page = $page;
        $this->view->total_results = $this->getTable('FedoraConnectorDatastream')->count();
        $this->view->results_per_page = get_option('per_page_admin');

    }

    /**
     * Delete datastream.
     *
     * @return void
     */
    public function deleteAction()
    {

        $id = $this->_request->id;
        $datastream = $this->getTable('FedoraConnectorDatastream')->find($id);

        // If delete confirmed, do delete.
        if ($this->_request->getParam('confirm') == 'true') {

            $datastream->delete();

            $this->flashError('Datastream deleted.');
            $this->_redirect('fedora-connector/datastreams');
            exit();

        }

        else {

            $this->view->datastream = $datastream;

        }

    }

    /**
     * Choose which item to add the datastream to.
     *
     * @return void
     */
    public function itemselectAction()
    {

        $sort_field = $this->_request->getParam('sort_field');
        $sort_dir = $this->_request->getParam('sort_dir');
        $search = $this->_request->getParam('search');

        // // Get the datastreams.
        $page = $this->_request->page;
        $order = fedorahelpers_doColumnSortProcessing($sort_field, $sort_dir);
        $items = fedorahelpers_getItems($page, $order, $search);

        $this->view->items = $items;
        $this->view->current_page = $page;
        $this->view->total_results = $this->getTable('Item')->count();
        $this->view->results_per_page = get_option('per_page_admin');
        $this->view->search = $search;

    }

    /**
     * PID form.
     *
     * @return void
     */
    public function getpidAction()
    {

        $item_id = $this->_request->id;
        $this->view->form = $this->_doPidForm($item_id);

    }

    /**
     * Select datastreams and metadata format.
     *
     * @return void
     */
    public function getdatastreamsAction()
    {

        $post = $this->_request->getPost();

        if (trim($post['pid']) == '') {
            $this->flashError('Enter a PID.');
            $this->_redirect('fedora-connector/datastreams/create/item/' . $post['item_id'] . '/pid');
            exit();
        }

        else {
            $server = $this->getTable('FedoraConnectorServer')->find($post['server_id']);
            if ($server->getDatastreamNodes($post['pid'])->length == 0) {
                $this->flashError('No nodes detected. Is the server and PID information correct?');
                $this->_redirect('fedora-connector/datastreams/create/item/' . $post['item_id'] . '/pid');
                exit();
            }
        }

        $this->view->form = $this->_doDatastreamsForm(
            $post['item_id'], $post['pid'], $post['server_id']);

    }

    /**
     * Creates the new datastream.
     *
     * @return void
     */
    public function insertdatastreamAction()
    {

        $post = $this->_request->getPost();

        $item_id = $post['item_id'];
        $pid = $post['pid'];
        $metadataformat = $post['metadataformat'];
        $server_id = $post['server_id'];

        $server = $this->getTable('FedoraConnectorServer')->find($server_id);

        foreach ($post['datastreams'] as $key => $stream) {

            $mime_type = $server->getMimeType($pid, $stream);

            $success[] = $this->getTable('FedoraConnectorDatastream')
                ->createDatastream(
                    array(
                        'item_id' => $item_id,
                        'server_id' => $server_id,
                        'pid' => $pid,
                        'datastream' => $stream,
                        'mime_type' => $mime_type,
                        'metadataformat' => $metadataformat
                    )
                );

            // DO TEI INSERT HERE.

        }

        if (!in_array(false, $success)) {

            $insertsCount = count($success);
            if ($insertsCount > 1) {
                $this->flashSuccess($insertsCount . ' datastreams added to item.');
            } else if ($insertsCount == 1) {
                $this->flashSuccess('Datastream added to item.');
            } else {
                $this->flashError('No datastreams were added.');
            }

            $this->_forward('browse', 'datastreams', 'fedora-connector');

        }

    }

    /**
     * Import datastream.
     *
     * @return void
     */
    public function importAction()
    {

        $id = $this->_request->id;
        $datastream = $this->getTable('FedoraConnectorDatastream')->find($id);

        switch ($datastream->metadata_stream) {

            case 'DC':
                fedora_importer_DC($datastream);
            break;

            case 'MODS':
                fedora_importer_MODS($datastream);
            break;

        }

        $this->flashSuccess('Metadata imported.');
        $this->_helper->redirector->goto($datastream->item_id, 'edit', 'items');

    }

    /**
     * Build the upload form.
     *
     * @param string $tmp The location of the temporary directory
     * where the tar files should be stored.
     *
     * @return object $form The upload form.
     */
    protected function _doPidForm($item_id) {

        $form = new Zend_Form();
        $form->setAction('datastreams')
            ->setMethod('post');

        $servers = $this->getTable('FedoraConnectorServer')->findAll();
        $serverSelect = new Zend_Form_Element_Select('server_id');

        foreach ($servers as $server) {
            $serverSelect->addMultiOption($server->id, $server->name);
            if ($server->is_default == 1) {
                $default = $server->id;
            }
        }

        $serverSelect->setValue($default);
        $serverSelect->setLabel('Server:');

        $pid = new Zend_Form_Element_Text('pid');
        $pid->setLabel('PID:')
            ->setRequired(true);

        $id = new Zend_Form_Element_Hidden('item_id');
        $id->setValue($item_id);

        $submit = new Zend_Form_Element_Submit('pid_submit');
        $submit->setLabel('Continue');

        $form->addElement($serverSelect);
        $form->addElement($pid);
        $form->addElement($id);
        $form->addElement($submit);

        return $form;

      }

    /**
     * Build the upload form.
     *
     * @param string $tmp The location of the temporary directory
     * where the tar files should be stored.
     *
     * @return object $form The upload form.
     */
    protected function _doDatastreamsForm($item_id = null, $pid = null, $server_id = null) {

        $form = new Zend_Form();
        $form->setAction('insertdatastream')
            ->setMethod('post');

        $server = $this->getTable('FedoraConnectorServer')->find($server_id);
        $datastreams = $server->getDatastreamNodes($pid);

        $datastreamSelect = new Zend_Form_Element_MultiCheckbox('datastreams');
        $datastreamSelect->setLabel('Datastreams:');

        $metadataformatSelect = new Zend_Form_Element_Select('metadataformat');
        $metadataformatSelect->setLabel('Metadata Format:');

        foreach ($datastreams as $datastream) {

            $dsid = $datastream->getAttribute('dsid');
            $is_text_xml = strpos($datastream->getAttribute('mimeType'), 'text/xml');
            $is_omitted = fedorahelpers_isOmittedDatastream($datastream);

            if (!$is_omitted) {
                $datastreamSelect->addMultiOption($dsid, $dsid);
            }

            if ($is_text_xml !== false && !$is_omitted) {
                $metadataformatSelect->addMultiOption($dsid, $dsid);
            }

        }

        $submit = new Zend_Form_Element_Submit('datastreams_submit');
        $submit->setLabel('Continue');

        $item_id_hidden = new Zend_Form_Element_Hidden('item_id');
        $item_id_hidden->setValue($item_id);

        $pid_hidden = new Zend_Form_Element_Hidden('pid');
        $pid_hidden->setValue($pid);

        $server_id_hidden = new Zend_Form_Element_Hidden('server_id');
        $server_id_hidden->setValue($server_id);

        $form->addElement($metadataformatSelect);
        $form->addElement($datastreamSelect);
        $form->addElement($submit);
        $form->addElement($item_id_hidden);
        $form->addElement($pid_hidden);
        $form->addElement($server_id_hidden);

        return $form;

      }









    /**
     * This retrieves and renders a set of datastream
     *
     * @return void
     */
    // public function selectAction()
    // {
    //     if ($_POST) {
    //         $form = $this->_getPidForm();

    //         if ($form->isValid($this->_request->getPost())) {
    //             // Get posted values.
    //             $uploadedData = $form->getValues();
    //             $item_id = ($uploadedData['fedora_connector_item_id'] > 0)
    //                 ? $uploadedData['fedora_connector_item_id']
    //                 : 0;
    //             $pid = $uploadedData['fedora_connector_pid'];
    //             $server_id = $uploadedData['fedora_connector_server_id'];

    //             $datastreamsForm = $this->_getDatastreamsForm(
    //                 $item_id,
    //                 $pid,
    //                 $server_id
    //             );
    //             $this->view->form = $datastreamsForm;
    //         }
    //     } else {
    //         $this->flashError('Failed to gather posted data.');
    //     }
    // }

    /**
     * This handles updating the form.
     *
     * @return void
     */
    // public function updateAction()
    // {
    //     // XXX some -> models/FedoraConnector/Datastream.php (updateFromArray)
    //     $form = $this->_getDatastreamsForms();

    //     if ($_POST) {
    //         $uploadedData = $this->_request->getPost();

    //         if ($form->isValid($uploadedData)) {
    //             // Get posted values.
    //             if ($uploadedData['fedora_connector_item_id'] > 0) {
    //                 $item_id = $uploadedData['fedora_connector_item_id'];
    //             } else {
    //                 $item = new Item;
    //                 $item->save();
    //                 $item_id = $item->id;
    //             }

    //             $pid = $uploadedData['fedora_connector_pid'];
    //             $metadataStream = $uploadedData['fedora_connector_metadata'];
    //             $server_id = $uploadedData['fedora_connector_server_id'];

    //             $posted = 0;
    //             foreach ($uploadedData as $k => $v) {
    //                 if (strpos($k, 'fedora_connector_datastream') !== false
    //                     && $v != '0'
    //                 ) {
    //                     $datastream = substr($k, 28); // Shave off the prefix.
    //                     $data = array(
    //                         'item_id'         => $item_id,
    //                         'pid'             => $pid,
    //                         'datastream'      => $datastream,
    //                         'mime_type'       => $v,
    //                         'metadata_stream' => $metadataStream,
    //                         'server_id'       => $server_id
    //                     );

    //                     if ($this->_updateDb($db, $datastream, $v, $data)) {
    //                         $posted += 1;
    //                     }

    //                 }
    //             }

    //             if ($posted > 0) {
    //                 $this->flashSuccess('Fedora datastreams connected to Item');
    //                 $this->_helper->redirector->goto($item_id, 'edit', 'items');
    //             } else {
    //                 $this->flashError('No datastreams selected.');
    //                 $this->_helper->redirector->goto($item_id, 'edit', 'items');
    //             }

    //         } else {
    //             // Ummm. No.
    //             // var_dump($this->_request->getPost());
    //             $this->flashError('Failed to gather posted data.');
    //         }
    //     }
    // }

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
    // private function _updateDb($db, $datastream, $value, $data)
    // {
    //     // XXX -> models/FedoraConnector/Datastream.php
    //     try {
    //         // Update the database with new values.
    //         $db = get_db();
    //         $fedoraconnector_id = $db->insert(
    //             'fedora_connector_datastreams',
    //             $data
    //         );

    //         // If TeiDisplay is installed, write an entry to 
    //         // the table if datastream is 'TEI'.
    //         if (function_exists('tei_display_installed')
    //             && $datastream == 'TEI'
    //             && strpos($value, 'text/xml') !== false
    //         ) {
    //             $this->_addTeiDatastream(
    //                 $db,
    //                 $datastream,
    //                 $fedoraconnector_id
    //             );
    //         }

    //         return true;

    //     } catch (Exception $err) {
    //         $this->flashError($err->getMessage());
    //         return false;
    //     }
    // }

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
    // private function _addTeiDatastream($db, $datastream, $fcId)
    // {
    //     // XXX -> models/FedoraConnector/Datastream.php

    //     $newDatastream = $db
    //         ->getTable('FedoraConnector_Datastream')
    //         ->find($fcId);
    //     $server = fedora_connector_get_server($newDatastream);
    //     $teiFile = fedora_connector_content_url($newDatastream, $server);

    //     // Get the TEI ID.
    //     $xml_doc = new DomDocument();
    //     $xml_doc->load($teiFile);

    //     $teiNode = $xml_doc->getElementsByTagName('TEI');
    //     $tei2Node = $xml_doc->getElementsByTagName('TEI.2');

    //     foreach ($teiNode as $teiNode) {
    //         $p5_id = $teiNode->getAttribute('xml:id');
    //     }
    //     foreach ($tei2Node as $tei2Node) {
    //         $p4_id = $tei2Node->getAttribute('id');
    //     }

    //     if (isset($p5_id)) {
    //         $tei_id = $p5_id;
    //     } else if (isset($p4_id)) {
    //         $tei_id = $p4_id;
    //     } else {
    //         $tei_id = null;
    //     }

    //     if ($tei_id != null){
    //         $teiData = array(
    //             'item_id'              => $item_id,
    //             'is_fedora_datastream' => 1,
    //             'fedoraconnector_id'   => $fcId,
    //             'tei_id'               => $tei_id
    //         );
    //         $db->insert('tei_display_configs', $teiData);
    //     }
    // }

    /**
     * This deletes an datastream and forwards to edit the item it was attached 
     * to.
     *
     * This only passes if the user is the current user.
     *
     * @return void
     */
    // // public function deleteAction()
    // // {
    // //     if ($user = $this->getCurrentUser()) {
    // //         $datastreamId = $this->_getParam('id');
    // //         $datastream = get_db()
    // //             ->getTable('FedoraConnector_Datastream')
    // //             ->find($datastreamId);
    // //         $item_id = $datastream->item_id;

    // //         $datastream->delete();

    // //         $this->flashSuccess('Fedora datastream successfully deleted.');
    // //         $this->_helper->redirector->goto($item_id, 'edit', 'items');

    // //     } else{
    // //         $this->_forward('forbidden');
    // //     }
    // // }

    /**
     * This imports an item's metadata.
     *
     * After successfully importing the metadata, this redirects to the item's 
     * edit page.
     *
     * @return void
     */
    // public function importAction()
    // {
    //     // XXX some -> libraries/FedoraConnector/Importer.php
    //     $id = $this->_getParam('id');
    //     $datastream = get_db()
    //         ->getTable('FedoraConnector_Datastream')
    //         ->find($id);

    //     echo fedora_connector_import_metadata($datastream);

    //     $this->flashSuccess('Metadata succesfully imported.');
    //     $this->_helper->redirector->goto($datastream->item_id, 'edit', 'items');
    // }

    /**
     * This builds the PID form for an item.
     *
     * @param integer $item_id The database ID for an item.
     *
     * @return Zend_Form The PID form object.
     */
    // private function _getPidForm($item_id)
    // {
    //     // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    //     $servers = get_db()
    //         ->getTable('FedoraConnector_Server')
    //         ->findAll();

    //     $form = new Zend_Form();
    //     $form->setAction('select');
    //     $form->setMethod('post');
    //     $form->setAttrib('enctype', 'multipart/form-data');

    //     $serverElement = new Zend_Form_Element_Select(
    //         'fedora_connector_server_id'
    //     );
    //     $serverElement->setLabel('Server:');
    //     foreach ($servers as $server) {
    //         $serverElement->addMultiOption($server->id, $server->name);
    //         if ($server->is_default > 0) {
    //             $is_default = $server->id;
    //         }
    //     }
    //     $serverElement->setValue($is_default);
    //     $serverElement->setRegisterInArrayValidator(false);
    //     // $metadataStream->setValue('MODS');
    //     $form->addElement($serverElement);

    //     // PID
    //     $pid = new Zend_Form_Element_Text('fedora_connector_pid');
    //     $pid->setLabel('PID:');
    //     $pid->setRequired(true);
    //     $form->addElement($pid);

    //     // Submit button
    //     $form->addElement('submit','submit');
    //     $submitElement=$form->getElement('submit');
    //     $submitElement->setLabel('Submit');

    //     // item id
    //     $itemId = new Zend_Form_Element_Hidden('fedora_connector_item_id');
    //     $itemId->setValue($item_id);
    //     $form->addElement($itemId);

    //     return $form;
    // }

    /**
     * This builds the datastreams form for an item.
     *
     * @param integer $item_id The database ID for an item.
     * @param integer $pid The PID for an item.
     * @param integer $server_id The database ID for the server.
     *
     * @return Zend_Form The form object.
     */
    // private function _getDatastreamsForms($item_id, $pid, $server_id)
    // {
    //     // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    //     // Get the server from the FedoraConnecter_Server table.
    //     $server = get_db()
    //         ->getTable('FedoraConnector_Server')
    //         ->find($server_id)
    //         ->url;

    //     // Get excluded datastreams.
    //     $omittedString = get_option('fedora_connector_omitted_datastreams');

    //     // Get the datastreams from Fedora REST.
    //     $datastreams = $this->_getDatastreamNodes($server, $pid);

    //     $form = Fedora_initForm('update', 'post', 'multipart/form-data');

    //     // List available datastreams to attach.
    //     foreach ($datastreams as $datastream) {
    //         // Skip datastream if in omitted list, e.g, RELS-INT, RELS-EXT, 
    //         // AUDIT.
    //         if (! $this->_isOmitted($datastream, $omittedString)) {
    //             $this->_addDatastreamInput($datastream, $form);
    //         }
    //     }

    //     $this->_addObjectMetadataSelect($datastreams, $omittedString, $form);
    //     Fedora_Form_addSubmit($form);
    //     Fedora_Form_addHidden($form, 'fedora_connector_item_id', $item_id);
    //     Fedora_Form_addHidden($form, 'fedora_connector_pid', $pid);
    //     Fedora_Form_addHidden($form, 'fedora_connector_server_id', $server_id);

    //     return $form;
    // }

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
    // private function _getDatastreamNodes($server, $pid)
    // {
    //     $datastreams = getQueryNodes(
    //         "{$server}objects/$pid/datastreams?format=xml",
    //         "//*[local-name() = 'datastream']"
    //     );
    //     return $datastreams;
    // }

    /**
     * This attaches the information from a datastream node to a form.
     *
     * @param DOMNode   $node The datastream node from the server response.
     * @param Zend_Form $form The form to add the node to.
     *
     * @return Zend_Form_Element_Checkbox The widget added to the form.
     */
    // private function _addDatastreamInput($node, $form)
    // {
    //     // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    //     $dsid = $node->getAttribute('dsid');

    //     $input = new Zend_Form_Element_Checkbox(
    //         'fedora_connector_datastream_' . $dsid
    //     );
    //     $input->setLabel($dsid);
    //     $input->setCheckedValue($node->getAttribute('mimeType'));

    //     $form->addElement($input);

    //     return $input;
    // }

    /**
     * This tests whether the node's dsid is in the omitted list.
     *
     * @param DOMNode $node The datastream node.
     * @param string $omit The list of datastream types in the omitted list.
     *
     * @return boolean True if the node should be omitted.
     */
    // private function _isOmitted($node, $omit)
    // {
    //     // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    //     return (strpos($omit, $node->getAttribute('dsid')) !== false);
    // }

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
    // private function _addObjectMetadataSelect($nodes, $omit, $form)
    // {
    //     // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    //     $select = new Zend_Form_Element_Select(
    //         'fedora_connector_metadata'
    //     );
    //     $select->setLabel('Object Metadata:');

    //     foreach ($nodes as $node) {
    //         if (strpos($node->getAttribute('mimeType'), 'text/xml') !== false
    //             && ! $this->_isOmitted($node, $omit)
    //         ) {
    //             $dsid = $datastream->getAttribute('dsid');
    //             $select->addMultiOption($dsid, $dsid);
    //         }
    //     }

    //     $select->setValue('DC');
    //     $select->setRegisterInArrayValidator(false);

    //     $form->addElement($select);

    //     return $select;
    // }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
