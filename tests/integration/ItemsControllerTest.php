<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Items controller integration tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_ItemsControllerTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * There should be a 'Fedora' tab in the item add form.
     *
     * @return void.
     */
    public function testItemAddTab()
    {

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Hit item add.
        $this->dispatch('items/add');

        // Check for tab.
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#fedora-metadata"]',
            'Fedora'
        );

        // Check markup.
        $this->assertXpath('//select[@id="server"][@name="server"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server1->id.'"]
          [@label="Test Title 1"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server2->id.'"]
          [@label="Test Title 2"]');
        $this->assertXpath('//input[@id="pid"][@name="pid"]');
        $this->assertXpath('//select[@id="dsids"][@name="dsids[]"]');
        $this->assertXpath('//input[@id="import"][@name="import"]');

    }

    /**
     * There should be a 'Fedora' tab in the item edit form.
     *
     * @return void.
     */
    public function testItemEditTab()
    {

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Create item.
        $item = $this->__item();

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Check for tab.
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#fedora-metadata"]',
            'Fedora'
        );

        // Check markup.
        $this->assertXpath('//select[@id="server"][@name="server"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server1->id.'"][@label="Test Title 1"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server2->id.'"][@label="Test Title 2"]');
        $this->assertXpath('//input[@id="pid"][@name="pid"]');
        $this->assertXpath('//select[@id="dsids"][@name="dsids[]"]');
        $this->assertXpath('//input[@id="import"][@name="import"]');

    }

    /**
     * If there is an existing Fedora object for the item, the data should be
     * populated in the textareas.
     *
     * @return void.
     */
    public function testItemEditData()
    {

        // Create item.
        $item = $this->__item();

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Create Fedora object.
        $object = $this->__object($item, $server2);

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Check server select and PID input.
        $this->assertXpath('//select[@id="server"][@name="server"]/option[@value="'.$server2->id.'"][@label="Test Title 2"][@selected="selected"]');
        $this->assertXpath('//input[@id="pid"][@name="pid"][@value="pid:test"]');

        // Check hidden fields.
        $this->assertXpath('//input[@type="hidden"][@name="datastreamsuri"]');
        $this->assertXpath('//input[@type="hidden"][@name="saveddsids"][@value="DC,content"]');

    }

    /**
     * When an item is added and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectCreationOnItemAdd()
    {

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'server' => 1,
                'pid' => 'pid:test',
                'dsids' => array('DC', 'content'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/add');

        // +1 editions.
        $this->assertEquals($this->objectsTable->count(), $count+1);

        // Get out service and check.
        $object = $this->objectsTable->find(1);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }

    /**
     * When an item is added and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    public function testImportOnItemAdd()
    {

    }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectCreationOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'server' => 1,
                'pid' => 'pid:test',
                'dsids' => array('DC', 'content'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // +1 editions.
        $this->assertEquals($this->objectsTable->count(), $count+1);

        // Get out service and check.
        $object = $this->objectsTable->find(1);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectUpdateOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Create Fedora object.
        $object = $this->__object($item);

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'server' => 1,
                'pid' => 'pid:test2',
                'dsids' => array('DC2', 'content2'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // +0 editions.
        $this->assertEquals($this->objectsTable->count(), $count);

        // Get out service and check.
        $object = $this->objectsTable->find(1);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test2');
        $this->assertEquals($object->dsids, 'DC2,content2');

    }

    /**
     * When an item is edited and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    public function testImportOnItemEdit()
    {

    }

}
