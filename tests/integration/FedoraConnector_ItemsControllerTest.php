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
        $item = new Item();
        $item->save();

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
        $item = new Item();
        $item->save();

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Create Fedora object.
        $object = new FedoraConnectorObject;
        $object->item_id = $item->id;
        $object->server_id = $server2->id;
        $object->pid = 'pid:test';
        $object->dsids = 'DC,content';
        $object->save();

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Check server select and PID input.
        $this->assertXpath('//select[@id="server"][@name="server"]/option[@value="'.$server2->id.'"][@label="Test Title 2"][@selected="selected"]');
        $this->assertXpath('//input[@id="pid"][@name="pid"][@value="pid:test"]');

        // Check hidden fields.
        $this->assertXpath('//input[@type="hidden"][@name="datastreamsuri"]');
        $this->assertXpath('//input[@type="hidden"][@name="saveddsids"][@value="DC,content"]');

    }

}
