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
        $item = fedorahelpers_getSingleItem($item_id);

        $this->view->item = $item;
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
        $item_id = $this->_request->id;
        $item = fedorahelpers_getSingleItem($item_id);

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

        $this->view->item = $item;
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

        // Get post data.
        $post = $this->_request->getPost();
        $item_id = $post['item_id'];
        $pid = $post['pid'];
        $metadataformat = $post['metadataformat'];
        $server_id = $post['server_id'];

        // Get the server object.
        $server = $this->getTable('FedoraConnectorServer')->find($server_id);

        // Now, add each checked datastream.
        foreach ($post['datastreams'] as $key => $stream) {

            // Query for the mime type.
            $mime_type = $server->getMimeType($pid, $stream);

            // Create the database record.
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

        }

        // Do feedback.
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

        // Not working...
        $importer = new FedoraConnecter_Importers;
        // $importer->import($datastream);

        // $this->flashSuccess('Metadata imported.');
        // $this->_helper->redirector->goto($datastream->item_id, 'edit', 'items');

        $this->_forward('browse', 'datastreams', 'fedora-connector');

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
            ->setMethod('post')
            ->setAttrib('class', 'fedora-connector-datastreams-form');

        $server = $this->getTable('FedoraConnectorServer')->find($server_id);
        $datastreams = $server->getDatastreamNodes($pid);

        $datastreamSelect = new Zend_Form_Element_MultiCheckbox('datastreams');
        $datastreamSelect->setLabel('Datastreams:');

        $metadataformatSelect = new Zend_Form_Element_Select('metadataformat');
        $metadataformatSelect->setLabel('Metadata Format:');

        foreach ($datastreams as $datastream) {

            $dsid = $datastream->getAttribute('dsid');
            $label = $datastream->getAttribute('label');
            $is_text_xml = strpos($datastream->getAttribute('mimeType'), 'text/xml');
            $is_omitted = fedorahelpers_isOmittedDatastream($datastream);

            if (!$is_omitted) {
                if ($label != '') {
                    $datastreamSelect->addMultiOption($dsid, $label);
                } else {
                    $datastreamSelect->addMultiOption($dsid, '[id: ' . $dsid . ']');
                }
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

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
