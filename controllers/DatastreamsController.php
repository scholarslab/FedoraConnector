<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_DatastreamsController
    extends Omeka_Controller_AbstractActionController
{


    /**
     * Cache the servers table.
     */
    public function init()
    {
        $this->_servers = $this->_helper->db->getTable(
            'FedoraConnectorServer'
        );
    }


    /**
     * Query for datastreams.
     * @ajax
     */
    public function indexAction()
    {

        // Supress the default Zend layout-sniffer functionality.
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-type', 'application/json');

        // Gather server and pid.
        $server = (int) $this->_request->server;
        $pid = $this->_request->pid;

        // Get datastreams.
        $server = $this->_servers->find($server);
        $nodes = $server->getDatastreamNodes($pid);

        // Construct array.
        $datastreams = array();
        foreach ($nodes as $node) {
            $datastreams[] = array(
                'dsid'  => $node->getAttribute('dsid'),
                'label' => $node->getAttribute('label')
            );
        }

        echo Zend_Json::encode($datastreams);

    }


}
