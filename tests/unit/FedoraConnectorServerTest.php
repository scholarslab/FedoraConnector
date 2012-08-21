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
     * When there are no servers and the saved server is not set to
     * active, set active.
     *
     * @return void.
     */
    public function testSaveInactiveServerWithNoServers()
    {

        // Create a record.
        $server = new FedoraConnectorServer();
        $server->is_default = 0;
        $server->save();

        // Check for active.
        $this->assertEquals($server->is_default, 1);

    }

    /**
     * When there is an existing active server and a new server is saved
     * with is_default = 1, toggle off old active server.
     *
     * @return void.
     */
    public function testSaveActiveServerWithExistingActiveServer()
    {

        // Create active server.
        $server1 = new FedoraConnectorServer();
        $server1->is_default = 1;
        $server1->save();

        // Create new active server.
        $server2 = new FedoraConnectorServer();
        $server2->is_default = 1;
        $server2->save();

        // Re-get and check for active.
        $server1 = $this->serversTable->find($server1->id);
        $server2 = $this->serversTable->find($server2->id);
        $this->assertEquals($server1->is_default, 0);
        $this->assertEquals($server2->is_default, 1);

    }

}
