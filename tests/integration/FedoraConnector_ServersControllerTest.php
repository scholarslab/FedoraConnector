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
     * Install the plugin.
     *
     * @return void.
     */
    public function setUp()
    {
        $this->setUpPlugin();
    }

    /**
     * Test for add form markup.
     *
     * @return void.
     */
    // public function testAddServerFormMarkup()
    // {

    //     $this->dispatch('fedora-connector/servers/add');
    //     $this->assertXpath('//input[@name="name"]');
    //     $this->assertXpath('//input[@name="url"]');
    //     $this->assertXpath('//input[@name="workspace"]');
    //     $this->assertXpath('//input[@name="username"]');
    //     $this->assertXpath('//input[@name="password"]');
    //     $this->assertXpath('//input[@name="active"]');

    // }

    /**
     * By default, active should be checked on add form.
     *
     * @return void.
     */
    // public function testAddServerDefaultActive()
    // {

    //     $this->dispatch('neatline-maps/servers/add');
    //     $this->assertXpath('//input[@name="active"][@checked="checked"]');

    // }

    /**
     * Test for form errors for empty fields.
     *
     * @return void.
     */
    // public function testAddServerEmptyFieldErrors()
    // {

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => '',
    //             'url' => '',
    //             'workspace' => '',
    //             'username' => '',
    //             'password' => ''
    //         )
    //     );

    //     $this->dispatch('neatline-maps/servers/add');
    //     $this->assertQueryCount('ul.errors', 5);
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a name.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a URL.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a workspace.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a username.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a password.');

    // }

    /**
     * Test for form error for invalid URL.
     *
     * @return void.
     */
    // public function testAddServerInvalidUrlError()
    // {

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'Test Server',
    //             'url' => 'invalid',
    //             'workspace' => 'workspace',
    //             'username' => 'admin',
    //             'password' => 'geoserver'
    //         )
    //     );

    //     $this->dispatch('neatline-maps/servers/add');
    //     $this->assertQueryCount('ul.errors', 1);
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a valid URL.');

    // }

    /**
     * Test for no form error for localhost URL.
     *
     * @return void.
     */
    // public function testAddServerLocalUrl()
    // {

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => '',
    //             'url' => 'http://localhost:8080/geoserver',
    //             'workspace' => '',
    //             'username' => '',
    //             'password' => ''
    //         )
    //     );

    //     $this->dispatch('neatline-maps/servers/add');
    //     $this->assertNotQueryContentContains('ul.errors li', 'Enter a valid URL.');

    // }

    /**
     * Valid form should create server.
     *
     * @return void.
     */
    // public function testAddServerSuccess()
    // {

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'Test Title',
    //             'url' => 'http://localhost:8080/geoserver',
    //             'workspace' => 'workspace',
    //             'username' => 'admin',
    //             'password' => 'geoserver',
    //             'active' => 1
    //         )
    //     );

    //     // Capture starting count.
    //     $count = $this->serversTable->count();

    //     // Add.
    //     $this->dispatch('neatline-maps/servers/add');

    //     // Check count+1.
    //     $this->assertEquals($this->serversTable->count(), $count+1);

    //     // Get new server, check params.
    //     $server = $this->serversTable->find(1);
    //     $this->assertEquals($server->name, 'Test Title');
    //     $this->assertEquals($server->url, 'http://localhost:8080/geoserver');
    //     $this->assertEquals($server->namespace, 'workspace');
    //     $this->assertEquals($server->username, 'admin');
    //     $this->assertEquals($server->password, 'geoserver');
    //     $this->assertEquals($server->active, 1);

    // }

    /**
     * If active is checked, existing active server should be toggled off.
     *
     * @return void.
     */
    // public function testAddServerActiveUpdating()
    // {

    //     // Create active server.
    //     $active = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'Test Title',
    //             'url' => 'http://localhost:8080/geoserver',
    //             'workspace' => 'workspace',
    //             'username' => 'admin',
    //             'password' => 'geoserver',
    //             'active' => 1
    //         )
    //     );

    //     // Add.
    //     $this->dispatch('neatline-maps/servers/add');

    //     // Get servers, check active statuses.
    //     $server1 = $this->serversTable->find(1);
    //     $server2 = $this->serversTable->find(2);
    //     $this->assertEquals($server1->active, 0);
    //     $this->assertEquals($server2->active, 1);

    // }

    /**
     * The /active route should set the server to active.
     *
     * @return void.
     */
    // public function testSetActive()
    // {

    //     // Create two servers.
    //     $this->__server();
    //     $this->__server();

    //     // At start, server2 is active.
    //     $server1 = $this->serversTable->find(1);
    //     $server2 = $this->serversTable->find(2);
    //     $this->assertEquals($server1->active, 0);
    //     $this->assertEquals($server2->active, 1);

    //     // Activate.
    //     $this->dispatch('neatline-maps/active/1');

    //     // Get servers, check active statuses.
    //     $server1 = $this->serversTable->find(1);
    //     $server2 = $this->serversTable->find(2);
    //     $this->assertEquals($server1->active, 1);
    //     $this->assertEquals($server2->active, 0);

    // }

    /**
     * Test for edit form markup.
     *
     * @return void.
     */
    // public function testEditServerFormMarkup()
    // {

    //     // Create server.
    //     $server = $this->__server(
    //         'Test Title',
    //         'http://localhost:8080/geoserver',
    //         'workspace',
    //         'admin',
    //         'geoserver',
    //         1
    //     );

    //     $this->dispatch('neatline-maps/edit/' . $server->id);
    //     $this->assertXpath('//input[@name="name"][@value="Test Title"]');
    //     $this->assertXpath('//input[@name="url"][@value="http://localhost:8080/geoserver"]');
    //     $this->assertXpath('//input[@name="workspace"][@value="workspace"]');
    //     $this->assertXpath('//input[@name="username"][@value="admin"]');
    //     $this->assertXpath('//input[@name="password"][@value="geoserver"]');
    //     $this->assertXpath('//input[@name="active"][@checked="checked"]');

    // }

    /**
     * Test for form errors for empty fields.
     *
     * @return void.
     */
    // public function testEditServerEmptyFieldErrors()
    // {

    //     // Create server.
    //     $server = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => '',
    //             'url' => '',
    //             'workspace' => '',
    //             'username' => '',
    //             'password' => ''
    //         )
    //     );

    //     $this->dispatch('neatline-maps/edit/' . $server->id);
    //     $this->assertQueryCount('ul.errors', 5);
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a name.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a URL.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a workspace.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a username.');
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a password.');

    // }

    /**
     * Test for form error for invalid URL.
     *
     * @return void.
     */
    // public function testEditServerInvalidUrlError()
    // {

    //     // Create server.
    //     $server = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'Test Server',
    //             'url' => 'invalid',
    //             'workspace' => 'workspace',
    //             'username' => 'admin',
    //             'password' => 'geoserver'
    //         )
    //     );

    //     $this->dispatch('neatline-maps/edit/' . $server->id);
    //     $this->assertQueryCount('ul.errors', 1);
    //     $this->assertQueryContentContains('ul.errors li', 'Enter a valid URL.');

    // }

    /**
     * Test for no form error for localhost URL.
     *
     * @return void.
     */
    // public function testEditServerLocalUrl()
    // {

    //     // Create server.
    //     $server = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => '',
    //             'url' => 'http://localhost:8080/geoserver',
    //             'workspace' => '',
    //             'username' => '',
    //             'password' => ''
    //         )
    //     );

    //     $this->dispatch('neatline-maps/edit/' . $server->id);
    //     $this->assertNotQueryContentContains('ul.errors li', 'Enter a valid URL.');

    // }

    /**
     * Valid form should edit server.
     *
     * @return void.
     */
    // public function testEditServerSuccess()
    // {

    //     // Create server.
    //     $server = $this->__server(
    //         'Test Title',
    //         'http://localhost:8080/geoserver',
    //         'workspace',
    //         'admin',
    //         'geoserver',
    //         1
    //     );

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'New Title',
    //             'url' => 'http://localhost:8888/geoserver',
    //             'workspace' => 'newworkspace',
    //             'username' => 'username',
    //             'password' => 'password',
    //             'active' => 1
    //         )
    //     );

    //     // Edit.
    //     $this->dispatch('neatline-maps/edit/' . $server->id);

    //     // Get new server, check params.
    //     $server = $this->serversTable->find(1);
    //     $this->assertEquals($server->name, 'New Title');
    //     $this->assertEquals($server->url, 'http://localhost:8888/geoserver');
    //     $this->assertEquals($server->namespace, 'newworkspace');
    //     $this->assertEquals($server->username, 'username');
    //     $this->assertEquals($server->password, 'password');
    //     $this->assertEquals($server->active, 1);

    // }

    /**
     * Valid form should edit server.
     *
     * @return void.
     */
    // public function testEditServerActiveUpdating()
    // {

    //     // Create servers.
    //     $server1 = $this->__server();
    //     $server2 = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array(
    //             'name' => 'New Title',
    //             'url' => 'http://localhost:8888/geoserver',
    //             'workspace' => 'newworkspace',
    //             'username' => 'username',
    //             'password' => 'password',
    //             'active' => 1
    //         )
    //     );

    //     // Edit.
    //     $this->dispatch('neatline-maps/edit/' . $server1->id);

    //     // Get servers, check statuses.
    //     $server1 = $this->serversTable->find(1);
    //     $server2 = $this->serversTable->find(2);
    //     $this->assertEquals($server1->active, 1);
    //     $this->assertEquals($server2->active, 0);

    // }

    /**
     * /delete should delete the server.
     *
     * @return void.
     */
    // public function testDeleteServer()
    // {

    //     // Create servers.
    //     $server1 = $this->__server();
    //     $server2 = $this->__server();

    //     // Form post.
    //     $this->request->setMethod('POST')
    //         ->setPost(array()
    //     );

    //     // Capture starting count.
    //     $count = $this->serversTable->count();

    //     // Delete.
    //     $this->dispatch('neatline-maps/delete/' . $server1->id);

    //     // Check count-1.
    //     $this->assertEquals($this->serversTable->count(), $count-1);

    //     // Check correct deletion.
    //     $this->assertNull($this->serversTable->find($server1->id));
    //     $this->assertNotNull($this->serversTable->find($server2->id));

    // }

}
