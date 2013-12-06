<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorServerTableTest extends FedoraConnector_Test_AppTestCase
{


    /**
     * `updateServer` should update a server record.
     */
    public function testUpdateServer()
    {

        // Create server.
        $server = $this->_server();

        // Mock post.
        $post = array(
            'name' => 'New Title',
            'url' => 'http://www.new.org/fedora'
        );

        // Pass in new data, re-get.
        $this->serversTable->updateServer($server, $post);
        $newServer = $this->serversTable->find($server->id);

        // Check for updated values.
        $this->assertEquals($newServer->name, 'New Title');
        $this->assertEquals($newServer->url, 'http://www.new.org/fedora');

    }


    /**
     * `updateServer` should remove a trailing slash off the URL.
     */
    public function testUpdateServerTrailingSlashRemoval()
    {

        // Create server.
        $server = $this->_server();

        // Mock post.
        $post = array(
            'name' => 'New Title',
            'url' => 'http://www.test.org/fedora/'
        );

        // Pass in new data, re-get.
        $this->serversTable->updateServer($server, $post);
        $newServer = $this->serversTable->find($server->id);

        // Check for updated values.
        $this->assertEquals($newServer->url, 'http://www.test.org/fedora');

    }


}
