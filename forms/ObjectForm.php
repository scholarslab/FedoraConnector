<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_Form_Object extends Omeka_Form
{


    private $_server;


    /**
     * Build the form.
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
            'description'   => __('Enter the PID of the Fedora resource.'),
            'size'          => 40
        ));

        // Datastream.
        $this->addElement('multiselect', 'dsids', array(
            'label'         => __('Datastreams'),
            'description'   => __('Choose one or more datastreams.'),
            'attribs'       => array('size' => 20),
            'multiOptions'  => array()
        ));

        // Import.
        $this->addElement('checkbox', 'import', array(
            'label'         => 'Import now?',
            'description'   => 'Import Fedora data when the Item is saved.'
        ));

        // Query datastreams uri.
        $this->addElement('hidden', 'datastreams-uri', array(
            'value'   => url('fedora-connector/datastreams/query-datastreams')
        ));

        // Saved dsid.
        $this->addElement('hidden', 'saved-dsids');

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
