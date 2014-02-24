<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_ServersControllerTest
    extends FedoraConnector_Case_Default
{


    /**
     * Test for add form markup.
     */
    public function testAddServerFormMarkup()
    {
        $this->dispatch('fedora-connector/servers/add');
        $this->assertXpath('//input[@name="name"]');
        $this->assertXpath('//input[@name="url"]');
    }


    /**
     * Test for form errors for empty fields.
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

        // Should flash name error.
        $this->assertXpath('//input[@name="name"]/
            following-sibling::ul[@class="error"]'
        );

        // Should flash URL error.
        $this->assertXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Test for form error for invalid URL.
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

        // Should flash URL error.
        $this->assertXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Test for no form error for localhost URL.
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

        // Should not flash URL error.
        $this->assertNotXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Valid form should create server.
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

        // Should create a server.
        $this->assertEquals($this->serversTable->count(), $count+1);

        // Get new server, check params.
        $server = $this->_getLastRow($this->serversTable);
        $this->assertEquals($server->name, 'Test Title');
        $this->assertEquals($server->url, 'http://localhost:8080/fedora');

    }


    /**
     * Test for edit form markup.
     */
    public function testEditServerFormMarkup()
    {

        // Create server.
        $server = $this->_server('title', 'url');

        // Should populate fields.
        $this->dispatch('fedora-connector/servers/edit/' . $server->id);
        $this->assertXpath('//input[@name="name"][@value="title"]');
        $this->assertXpath('//input[@name="url"][@value="url"]');

    }


    /**
     * Test for form errors for empty fields.
     */
    public function testEditServerEmptyFieldErrors()
    {

        // Create server.
        $server = $this->_server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name'  => '',
                'url'   => ''
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);

        // Should flash name error.
        $this->assertXpath('//input[@name="name"]/
            following-sibling::ul[@class="error"]'
        );

        // Should flash URL error.
        $this->assertXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Test for form error for invalid URL.
     */
    public function testEditServerInvalidUrlError()
    {

        // Create server.
        $server = $this->_server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'Test Server',
                'url' => 'invalid'
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);

        // Should flash URL error.
        $this->assertXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Test for no form error for localhost URL.
     */
    public function testEditServerLocalUrl()
    {

        // Create server.
        $server = $this->_server();

        // Form post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => '',
                'url' => 'http://localhost:8080/fedora',
            )
        );

        $this->dispatch('fedora-connector/servers/edit/' . $server->id);

        // Should flash not URL error.
        $this->assertNotXpath('//input[@name="url"]/
            following-sibling::ul[@class="error"]'
        );

    }


    /**
     * Valid form should edit server.
     */
    public function testEditServerSuccess()
    {

        // Create server.
        $server = $this->_server(
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
        $server = $this->serversTable->find($server->id);
        $this->assertEquals($server->name, 'New Title');
        $this->assertEquals($server->url, 'http://test.org/fedora');

    }


}
