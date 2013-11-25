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

    /**
     * Test for form errors for empty fields.
     *
     * @return void.
     */
    public function testAddServerEmptyFieldErrors()
    {

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => '',
                'url' => ''
            )
        );

        $this->dispatch('fedora-connector/servers/add');
        $this->assertQueryCount('ul.error', 2);
        $this->assertQueryContentContains('ul.error li', 'Enter a name.');
        $this->assertQueryContentContains('ul.error li', 'Enter a URL.');

    }

    /**
     * Test for form error for invalid URL.
     *
     * @return void.
     */
    public function testAddServerInvalidUrlError()
    {

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'Test Server',
                'url' => 'invalid'
            )
        );

        $this->dispatch('fedora-connector/servers/add');
        $this->assertQueryCount('ul.error', 1);
        $this->assertQueryContentContains('ul.error li', 'Enter a valid URL.');

    }

    /**
     * Test for no form error for localhost URL.
     *
     * @return void.
     */
    public function testAddServerLocalUrl()
    {

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => '',
                'url' => 'http://localhost:8080/fedora'
            )
        );

        $this->dispatch('fedora-connector/servers/add');
        $this->assertNotQueryContentContains('ul.error li', 'Enter a valid URL.');

    }

    /**
     * Valid form should create server.
     *
     * @return void.
     */
    public function testAddServerSuccess()
    {

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'Test Title',
                'url' => 'http://localhost:8080/fedora'
            )
        );

        // Capture starting count.
        $count = $this->serversTable->count();

        // Add.
        $this->dispatch('fedora-connector/servers/add');

        // Check count+1.
        $this->assertEquals($this->serversTable->count(), $count+1);

        // Get new server, check params.
        $server = $this->serversTable->find(1);
        $this->assertEquals($server->name, 'Test Title');
        $this->assertEquals($server->url, 'http://localhost:8080/fedora');

    }

    /**
     * Test for edit form markup.
     *
     * @return void.
     */
    public function testEditServerFormMarkup()
    {

        // Create server.
        $server = $this->__server(
            'Test Title',
            'http://localhost:8080/fedora'
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);
        $this->assertXpath('//input[@name="name"][@value="Test Title"]');
        $this->assertXpath('//input[@name="url"][@value="http://localhost:8080/fedora"]');

    }

    /**
     * Test for form errors for empty fields.
     *
     * @return void.
     */
    public function testEditServerEmptyFieldErrors()
    {

        // Create server.
        $server = $this->__server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => '',
                'url' => ''
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);
        $this->assertQueryCount('ul.error', 2);
        $this->assertQueryContentContains('ul.error li', 'Enter a name.');
        $this->assertQueryContentContains('ul.error li', 'Enter a URL.');

    }

    /**
     * Test for form error for invalid URL.
     *
     * @return void.
     */
    public function testEditServerInvalidUrlError()
    {

        // Create server.
        $server = $this->__server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'Test Server',
                'url' => 'invalid'
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);
        $this->assertQueryCount('ul.error', 1);
        $this->assertQueryContentContains('ul.error li', 'Enter a valid URL.');

    }

    /**
     * Test for no form error for localhost URL.
     *
     * @return void.
     */
    public function testEditServerLocalUrl()
    {

        // Create server.
        $server = $this->__server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => '',
                'url' => 'http://localhost:8080/fedora',
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);
        $this->assertNotQueryContentContains('ul.error li', 'Enter a valid URL.');

    }

    /**
     * Valid form should edit server.
     *
     * @return void.
     */
    public function testEditServerSuccess()
    {

        // Create server.
        $server = $this->__server(
            'Test Title',
            'http://localhost:8080/fedora'
        );

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'New Title',
                'url' => 'http://test.org/fedora'
            )
        );

        // Edit.
        $this->dispatch('fedora-connector/servers/edit/' . $server->id);

        // Get new server, check params.
        $server = $this->serversTable->find(1);
        $this->assertEquals($server->name, 'New Title');
        $this->assertEquals($server->url, 'http://test.org/fedora');

    }

}
