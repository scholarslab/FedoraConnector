<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Object table tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnectorObjectTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * Test attribute access.
     *
     * @return void.
     */
    public function testAttributeAccess()
    {

        // Create object.
        $item = $this->__item();
        $server = $this->__server();
        $object = $this->__object($item, $server);

        // Check attributes.
        $this->assertEquals($object->item_id, $item->id);
        $this->assertEquals($object->server_id, $server->id);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }

    /**
     * getServer() should return the parent server record.
     *
     * @return void.
     */
    public function testGetServer()
    {

        // Create object.
        $item = $this->__item();
        $server = $this->__server();
        $object = $this->__object($item, $server);

        // Get parent server.
        $retrievedServer = $object->getServer();
        $this->assertEquals($retrievedServer->id, $server->id);

    }

    /**
     * getItem() should return the parent item record.
     *
     * @return void.
     */
    public function testGetItem()
    {

        // Create object.
        $item = $this->__item();
        $server = $this->__server();
        $object = $this->__object($item, $server);

        // Get parent item.
        $retrievedItem = $object->getItem();
        $this->assertEquals($retrievedItem->id, $item->id);

    }

    /**
     * getMetadataUrl() should construct the URL the metadata stream
     * for the pid/dsid.
     *
     * @return void.
     */
    public function testGetMetatadaUrl()
    {

        // Create object.
        $object = $this->__object();

        // Mock the Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Get the url.
        $this->assertEquals(
            $object->getMetadataUrl('DC'),
            'http://www.test.org/fedora/objects/pid:test/datastreams/DC/content'
        );

    }

}
