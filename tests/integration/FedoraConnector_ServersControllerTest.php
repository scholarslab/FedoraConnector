<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Servers controller integration tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_ServersControllerTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * Test for add form markup.
     *
     * @return void.
     */
    public function testAddServerFormMarkup()
    {
        $this->dispatch('fedora-connector/servers/add');
        $this->assertXpath('//input[@name="name"]');
        $this->assertXpath('//input[@name="url"]');
    }

}
