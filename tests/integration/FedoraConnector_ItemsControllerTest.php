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

        // Check for textareas.
        $this->assertXpath('//select[@id="server"][@name="server"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server1->id.'"][@label="Test Title 1"]');
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server2->id.'"][@label="Test Title 2"]');
        $this->assertXpath('//input[@id="pid"][@name="pid"]');
        $this->assertXpath('//select[@id="dsids"][@name="dsids[]"]');
        $this->assertXpath('//input[@id="import"][@name="import"]');

    }

}
