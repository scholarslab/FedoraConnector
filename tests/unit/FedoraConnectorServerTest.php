<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorServerTest extends FedoraConnector_Test_AppTestCase
{


    /**
     * Test attribute access.
     */
    public function testAttributeAccess()
    {

        // Create object.
        $server = $this->_server('Test Title', 'http://test.org/fedora');

        // Check attributes.
        $this->assertEquals($server->name, 'Test Title');
        $this->assertEquals($server->url, 'http://test.org/fedora');

    }


    /**
     * `getVersion` should return the server version.
     */
    public function testGetVersion()
    {

        // Mock the Fedora response.
        $this->_mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Get version.
        $this->assertEquals($server->getVersion(), '3.4.2');

    }


    /**
     * `getVersion` should return false when the server is offline.
     */
    public function testGetVersionWhenOffline()
    {

        // Mock the Fedora response.
        $this->_mockFedora(
            'empty.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Get version.
        $this->assertFalse($server->getVersion());

    }


    /**
     * `getService` should return the correct url part for 2.x versions.
     */
    public function testGetService2x()
    {

        // Mock 2.x Fedora response.
        $this->_mockFedora(
            'describe-v2x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Get service.
        $this->assertEquals($server->getService(), 'get');

    }


    /**
     * `getService` should return the correct url part for 3.x versions.
     */
    public function testGetService3x()
    {

        // Mock 3.x Fedora response.
        $this->_mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Get service.
        $this->assertEquals($server->getService(), 'objects');

    }


    /**
     * `isOnline` should return false when the server does not respond.
     */
    public function testIsOnlineWhenOffline()
    {

        // Mock Fedora response.
        $this->_mockFedora(
            'empty.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Ping.
        $this->assertFalse($server->isOnline());

    }


    /**
     * `isOnline` should return true when the server responds.
     */
    public function testIsOnlineWhenOnline()
    {

        // Mock Fedora response.
        $this->_mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->_server();

        // Ping.
        $this->assertTrue($server->isOnline());

    }


    /**
     * `getDatastreamNodes` should return the datastream nodes.
     */
    public function testGetDatastreamNodes()
    {

        // Mock Fedora response.
        $this->_mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->_server();

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
     * `getMimeType` should return the mime type.
     */
    public function testGetMimeType()
    {

        // Mock Fedora response.
        $this->_mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream'][@dsid = 'DC']"
        );

        // Create server.
        $server = $this->_server();

        // Get nodes.
        $this->assertEquals(
            $server->getMimeType('pid:test', 'DC'), 'text/xml'
        );

    }


}
