<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_DatastreamsControllerTest
    extends FedoraConnector_Test_AppTestCase
{


    /**
     * /query-datastreams should return a list of datastreams.
     */
    public function testQueryDatastreams()
    {

        $this->__mockFedora(
            'datastreams.xml', "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->__server();

        // Mock POST.
        $this->request->setMethod('GET') ->setParams(array(
            'server' => $server->id,
            'pid' => 'pid:test'
        ));

        // Query for datastreams.
        $this->dispatch('fedora-connector/datastreams/query-datastreams');
        $json = Zend_Json::decode($this->getResponse()->getBody('default'));

        $this->assertEquals($json, array(
            array(
                'dsid'  => 'DC',
                'label' => 'Dublin Core Record'
            ),
            array(
                'dsid'  => 'descMetadata',
                'label' => 'MODS descriptive metadata'
            ),
            array(
                'dsid'  => 'rightsMetadata',
                'label' => 'Hydra-compliant access rights metadata'
            ),
            array(
                'dsid'  => 'POLICY',
                'label' => 'Fedora-required policy datastream'
            ),
            array(
                'dsid'  => 'RELS-INT',
                'label' => 'Datastream Relationships'
            ),
            array(
                'dsid'  => 'technicalMetadata',
                'label' => 'Technical metadata'
            ),
            array(
                'dsid'  => 'RELS-EXT',
                'label' => 'Object Relationships'
            ),
            array(
                'dsid'  => 'solrArchive',
                'label' => 'Index Data for Posting to Solr'
            ),
            array(
                'dsid'  => 'content',
                'label' => 'JPEG-2000 Binary'
            )
        ));

    }


}
