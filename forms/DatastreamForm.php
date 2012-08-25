<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Server form.
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
 * @subpackage  fedoraconector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_Form_Datastream extends Omeka_Form
{

    private $_server;

    /**
     * Build the form.
     *
     * @return void.
     */
    public function init()
    {

        parent::init();

        $this->setMethod('post');
        $this->setAttrib('id', 'datastream-form');
        $this->addElementPrefixPath('FedoraConnector', dirname(__FILE__));

        // Server.
        $this->addElement('select', 'server', array(
            'label'         => __('Server'),
            'description'   => __('Select a Fedora server.'),
            'multiOptions'  => $this->getServersForSelect()
        ));

        // PID.
        $this->addElement('text', 'pid', array(
            'label'         => __('PID'),
            'description'   => __('Enter the object PID of the Fedora resource.'),
            'size'          => 40
        ));

        // Datastream.
        $this->addElement('select', 'dsid', array(
            'label'         => __('Datastream'),
            'description'   => __('Choose a datastream.'),
            'attribs'       => array('size' => 20),
            'multiOptions'  => array()
        ));

        // Import.
        $this->addElement('checkbox', 'import', array(
            'label'         => 'Import now?',
            'description'   => 'Import Fedora data when the Item form is saved.'
        ));

        // Query datastreams uri.
        $this->addElement('hidden', 'datastreams-uri', array(
            'value'   => uri('fedora-connector/datastreams/query-datastreams')
        ));

    }

    /**
     * Get the list of Fedora servers.
     *
     * @return array $servers The server.
     */
    public function getServersForSelect()
    {

        // Get file table.
        $_db = get_db();
        $_servers = $_db->getTable('FedoraConnectorServer');

        // Fetch.
        $records = $_servers->findAll();

        // Build the array.
        $servers = array();
        foreach($records as $record) {
            $servers[$record->id] = $record->name;
        };

        return $servers;

    }

}
