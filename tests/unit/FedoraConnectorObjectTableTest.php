<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorObjectTableTest extends FedoraConnector_Test_AppTestCase
{


    /**
     * `findByItem` should get the object for an item.
     */
    public function testFindByItemWhenRecordExists()
    {

        // Create item and object.
        $item = $this->__item();
        $object = $this->__object($item);

        // Retrieve.
        $retrievedObject = $this->objectsTable->findByItem($item);
        $this->assertEquals($retrievedObject->id, $object->id);

    }


    /**
     * `findByItem` should should return false when no object exists.
     */
    public function testFindByItemWhenNoRecordExists()
    {

        // Create item and object.
        $item = $this->__item();

        // Try to get out an object.
        $this->assertFalse($this->objectsTable->findByItem($item));

    }


    /**
     * `createOrUpdate` should create a new record when one does not exist.
     */
    public function testCreateOrUpdateWithNoRecord()
    {

        // Create item and server.
        $item = $this->__item();
        $server = $this->__server();

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Create new record.
        $object = $this->objectsTable->createOrUpdate(
            $item, $server->id, 'pid:test', array('DC','content'));

        // Check for count++.
        $this->assertEquals($this->objectsTable->count(), $count+1);

        // Check attributes.
        $this->assertEquals($object->item_id, $item->id);
        $this->assertEquals($object->server_id, $server->id);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }


    /**
     * `createOrUpdate` should update an existing record when one exists.
     */
    public function testCreateOrUpdateWithExistingRecord()
    {

        // Create item and servers.
        $item = $this->__item();
        $server1 = $this->__server();
        $server2 = $this->__server();

        // Create object.
        $object = $this->__object($item, $server1, 'pid:test', 'DC,content');

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Update record.
        $object = $this->objectsTable->createOrUpdate(
            $item, $server2->id, 'pid:new', array('DC', 'new'));

        // Check for count.
        $this->assertEquals($this->objectsTable->count(), $count);

        // Check attributes.
        $this->assertEquals($object->item_id, $item->id);
        $this->assertEquals($object->server_id, $server2->id);
        $this->assertEquals($object->pid, 'pid:new');
        $this->assertEquals($object->dsids, 'DC,new');

    }


}
