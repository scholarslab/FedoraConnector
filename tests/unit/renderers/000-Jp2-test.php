<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class Jp2RendererTest extends FedoraConnector_Test_AppTestCase
{


    public function setUp()
    {
        parent::setup();
        $this->renderer = new Jp2_Renderer();
    }


    /**
     * `__construct` should initialize sizes array.
     */
    public function testConstruct()
    {
        $this->assertArrayHasKey('thumb', $this->renderer->sizes);
        $this->assertArrayHasKey('screen', $this->renderer->sizes);
        $this->assertArrayHasKey('*', $this->renderer->sizes);
    }


    /**
     * `canDisplay` should accept the image/jp2 mimetype.
     */
    public function testCanDisplay()
    {
        $this->assertTrue($this->renderer->canDisplay('image/jp2'));
        $this->assertFalse($this->renderer->canDisplay('invalid'));
    }


    /**
     * `display` should form the correct image tag.
     */
    public function testDisplay()
    {

        // Create server.
        $server = $this->_server();

        // Mock Fedora.
        $this->_mockFedora(
            'describe-v3x.xml',
            "//*[local-name() = 'repositoryVersion']"
        );

        // Construct image URL.
        $url = "{$server->url}/{$server->getService()}/{$object->pid}"
            . "/methods/djatoka:jp2SDef/getRegion?scale={$px},{$px}";

        $this->assertEquals(
            $this->renderer->display(),
            "<img alt='image' src='{$url}' />"
        );

    }


}
