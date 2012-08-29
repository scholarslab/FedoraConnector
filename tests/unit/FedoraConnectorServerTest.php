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
     * getVersion() should return the Fedora version.
     *
     * @return void.
     */
    public function testGetVersion()
    {

        // Generate response fixture.
        $gateway = new FedoraGateway();
        $url = FEDORA_CONNECTOR_PLUGIN_DIR . '/tests/fixtures/describe.xml';
        $xpath = "//*[local-name() = 'repositoryVersion']";
        $fixture = $gateway->query($url, $xpath);

        // Create server.
        $server = $this->__server();

        // Stub gateway.
        $gateway = $this->getMock('FedoraGateway')
            ->expects($this->any())
            ->method('query')
            ->will($this->returnValue($fixture));

        // Get version.
        $this->assertEquals($server->getVersion(), '3.4.2');

    }

}
