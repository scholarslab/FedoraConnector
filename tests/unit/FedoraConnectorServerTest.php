<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Server table tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnectorServerTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * Test attribute access.
     *
     * @return void.
     */
    public function testAttributeAccess()
    {

        // Create object.
        $server = $this->__server('Test Title', 'http://test.org/fedora');

        // Check attributes.
        $this->assertEquals($server->name, 'Test Title');
        $this->assertEquals($server->url, 'http://test.org/fedora');

    }

    /**
     * getVersion() should return the server version.
     *
     * @return void.
     */
    public function testGetVersion()
    {

        // Mock the Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get version.
        $this->assertEquals($server->getVersion(), '3.4.2');

    }

    /**
     * getVersion() should return false when the server is offline.
     *
     * @return void.
     */
    public function testGetVersionWhenOffline()
    {

        // Mock the Fedora response.
        $this->__mockFedora(
            'empty.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get version.
        $this->assertFalse($server->getVersion());

    }

    /**
     * getService() should return the correct url part for 2.x versions.
     *
     * @return void.
     */
    public function testGetService2x()
    {

        // Mock 2.x Fedora response.
        $this->__mockFedora(
            'describe-v2x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get service.
        $this->assertEquals($server->getService(), 'get');

    }

    /**
     * getService() should return the correct url part for 3.x versions.
     *
     * @return void.
     */
    public function testGetService3x()
    {

        // Mock 3.x Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get service.
        $this->assertEquals($server->getService(), 'objects');

    }

    /**
     * isOnline() should return false when the server does not respond.
     *
     * @return void.
     */
    public function testIsOnlineWhenOffline()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'empty.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Ping.
        $this->assertFalse($server->isOnline());

    }

    /**
     * isOnline() should return true when the server responds.
     *
     * @return void.
     */
    public function testIsOnlineWhenOnline()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Ping.
        $this->assertTrue($server->isOnline());

    }

    /**
     * getDatastreamNodes() should return the datastream nodes.
     *
     * @return void.
     */
    public function testGetDatastreamNodes()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->__server();

        // Get nodes.
        $nodes = $server->getDatastreamNodes('pid:test');
        $this->assertEquals($nodes->item(0)->getAttribute('dsid'), 'DC');
        $this->assertEquals($nodes->item(1)->getAttribute('dsid'), 'descMetadata');
        $this->assertEquals($nodes->item(2)->getAttribute('dsid'), 'rightsMetadata');
        $this->assertEquals($nodes->item(3)->getAttribute('dsid'), 'POLICY');
        $this->assertEquals($nodes->item(4)->getAttribute('dsid'), 'RELS-INT');
        $this->assertEquals($nodes->item(5)->getAttribute('dsid'), 'technicalMetadata');
        $this->assertEquals($nodes->item(6)->getAttribute('dsid'), 'RELS-EXT');
        $this->assertEquals($nodes->item(7)->getAttribute('dsid'), 'solrArchive');
        $this->assertEquals($nodes->item(8)->getAttribute('dsid'), 'content');

    }

    /**
     * getMimeType() should return the mime type.
     *
     * @return void.
     */
    public function testGetMimeType()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream'][@dsid = 'DC']"
        );

        // Create server.
        $server = $this->__server();

        // Get nodes.
        $this->assertEquals(
            $server->getMimeType('pid:test', 'DC'), 'text/xml'
        );

    }

}
