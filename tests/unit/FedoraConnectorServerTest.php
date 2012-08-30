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
     * getVersion() should return the server version.
     *
     * @return void.
     */
    public function testGetVersion()
    {

        // Mock the Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get version.
        $this->assertEquals($server->getVersion(), '3.4.2');

    }

    /**
     * getService() should return the correct url part for 2.x versions.
     *
     * @return void.
     */
    public function testGetService2x()
    {

        // Mock 2.x Fedora response.
        $this->__mockFedora(
            'describe-v2x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get version.
        $this->assertEquals($server->getService(), 'get');

    }

    /**
     * getService() should return the correct url part for 3.x versions.
     *
     * @return void.
     */
    public function testGetService3x()
    {

        // Mock 2.x Fedora response.
        $this->__mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Create server.
        $server = $this->__server();

        // Get version.
        $this->assertEquals($server->getService(), 'objects');

    }

}
