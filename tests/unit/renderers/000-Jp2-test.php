<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Jp2 renderer unit tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class Jp2RendererTest extends FedoraConnector_Test_AppTestCase
{

    public function setUp()
    {
        $this->renderer = new Jp2_Renderer();
    }

    /**
     * __construct() should initialize sizes array.
     *
     * @return void.
     */
    public function testConstruct()
    {
        $this->assertArrayHasKey('thumb', $this->renderer->sizes);
        $this->assertArrayHasKey('screen', $this->renderer->sizes);
        $this->assertArrayHasKey('*', $this->renderer->sizes);
    }

    /**
     * canDisplay() should accept the image/jp2 mimetype.
     *
     * @return void.
     */
    public function testCanDisplay()
    {
        $this->assertTrue($this->renderer->canDisplay('image/jp2'));
        $this->assertFalse($this->renderer->canDisplay('invalid'));
    }

    /**
     * display() should form the correct image tag.
     *
     * @return void.
     */
    public function testDisplay()
    {

        // Create server.
        $server = $this->__server();

        // Mock Fedora.
        $this->__mockFedora(
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
