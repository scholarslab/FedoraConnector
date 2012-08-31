<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Datastreams controller integration tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_DatastreamsControllerTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * /query-datastreams should return a list of datastreams.
     *
     * @return void.
     */
    public function testQueryDatastreams()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->__server();

        // Mock POST.
        $this->request->setMethod('GET')
            ->setParams(array(
                'server' => $server->id,
                'pid' => 'pid:test'
            )
        );

        // Hit /query-datastreams.
        $this->dispatch('fedora-connector/datastreams/query-datastreams');
        $response = $this->getResponse()->getBody('default');
        $json = json_decode($response);

        // Check datastreams.
        $this->assertEquals($json, array(
            (object) array('dsid' => 'DC', 'label' => 'Dublin Core Record'),
            (object) array('dsid' => 'descMetadata', 'label' => 'MODS descriptive metadata'),
            (object) array('dsid' => 'rightsMetadata', 'label' => 'Hydra-compliant access rights metadata'),
            (object) array('dsid' => 'POLICY', 'label' => 'Fedora-required policy datastream'),
            (object) array('dsid' => 'RELS-INT', 'label' => 'Datastream Relationships'),
            (object) array('dsid' => 'technicalMetadata', 'label' => 'Technical metadata'),
            (object) array('dsid' => 'RELS-EXT', 'label' => 'Object Relationships'),
            (object) array('dsid' => 'solrArchive', 'label' => 'Index Data for Posting to Solr'),
            (object) array('dsid' => 'content', 'label' => 'JPEG-2000 Binary')
        ));

    }

}
