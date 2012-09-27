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

class FedoraConnectorServerTableTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * updateServer() should update a server record.
     *
     * @return void.
     */
    public function testUpdateServer()
    {

        // Create server.
        $server = $this->__server();

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
     * updateServer() should remove a trailing slash off the URL.
     *
     * @return void.
     */
    public function testUpdateServerTrailingSlashRemoval()
    {

        // Create server.
        $server = $this->__server();

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
