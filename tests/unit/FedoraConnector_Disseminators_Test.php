<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
 * institutional repositories in their Omeka repositories.
 *
 * The FedoraConnector plugin provides methods to generate calls against Fedora-
 * based content disemminators. Unlike traditional ingestion techniques, this
 * plugin provides a facade to Fedora-Commons repositories and records pointers
 * to the "real" objects rather than creating new physical copies. This will
 * help ensure longer-term durability of the content streams, as well as allow
 * you to pull from multiple institutions with open Fedora-Commons
 * respositories.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      Ethan Gruber <ewg4x@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      Wayne Graham <wayne.graham@virginia.edu>
 * @author      Eric Rochester <err8n@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

require_once __DIR__ . '/../../libraries/FedoraConnector/Disseminators.php';


class FedoraConnector_Disseminators_Test extends PHPUnit_Framework_TestCase
{
    var $diss;

    function setUp() {
        $this->diss = new FedoraConnector_Disseminators(__DIR__ . '/diss');
    }

    function testGetDisseminators() {
        $dissList = $this->diss->getDisseminators();
        $this->assertEquals(4, count($dissList));

        $expected = array(
            'TextPlain_Disseminator',
            'ImageGif_Disseminator',
            'XmlTei_Disseminator',
            'Default_Disseminator'
        );

        for ($i=0; $i<count($dissList); $i++) {
            $diss = $dissList[$i];
            $this->assertEquals($expected[$i], get_class($diss));
        }
    }

    function testHasDisseminatorFor() {
        $this->assertTrue(
            $this->diss->hasDisseminatorFor('text/plain', null)
        );
        $this->assertTrue(
            $this->diss->hasDisseminatorFor('image/gif', null)
        );
        $this->assertTrue(
            $this->diss->hasDisseminatorFor('text/xml', null)
        );
        $this->assertFalse(
            $this->diss->hasDisseminatorFor('something/else', null)
        );
    }

    function testGetDisseminator() {
        $this->assertEquals(
            'TextPlain_Disseminator',
            get_class($this->diss->getDisseminator('text/plain', null))
        );
        $this->assertEquals(
            'ImageGif_Disseminator',
            get_class($this->diss->getDisseminator('image/gif', null))
        );
        $this->assertEquals(
            'XmlTei_Disseminator',
            get_class($this->diss->getDisseminator('text/xml', null))
        );
        $this->assertEquals(
            'Default_Disseminator',
            get_class($this->diss->getDisseminator('text/unknown', null))
        );
        $this->assertNull(
            $this->diss->getDisseminator('something/else', null)
        );
    }

    function testCanHandle() {
        $this->assertTrue(
            $this->diss->canHandle('text/plain', null)
        );
        $this->assertTrue(
            $this->diss->canHandle('image/gif', null)
        );
        $this->assertTrue(
            $this->diss->canHandle('text/xml', null)
        );
        $this->assertTrue(
            $this->diss->canHandle('text/unknown', null)
        );
    }

    function testCanHandleFalse() {
        $this->assertFalse(
            $this->diss->canHandle('something/else', null)
        );
    }

    function testHandle() {
        $this->assertEquals(
            'TextPlain_Disseminator',
            $this->diss->handle('text/plain', null)
        );
        $this->assertEquals(
            'ImageGif_Disseminator',
            $this->diss->handle('image/gif', null)
        );
        $this->assertEquals(
            'XmlTei_Disseminator',
            $this->diss->handle('text/xml', null)
        );
        $this->assertEquals(
            'Default_Disseminator',
            $this->diss->handle('text/unknown', null)
        );
    }

    function testHandleNull() {
        $this->assertNull(
            $this->diss->handle('text/something', null)
        );
    }

    function testCanPreview() {
        $this->assertTrue(
            $this->diss->canPreview('text/plain', null)
        );
        $this->assertTrue(
            $this->diss->canPreview('image/gif', null)
        );
        $this->assertTrue(
            $this->diss->canPreview('text/xml', null)
        );
        $this->assertTrue(
            $this->diss->canPreview('text/unknown', null)
        );
    }

    function testCanPreviewFalse() {
        $this->assertFalse(
            $this->diss->canPreview('something/else', null)
        );
    }

    function testPreview() {
        $this->assertEquals(
            'TextPlain_Disseminator Preview',
            $this->diss->preview('text/plain', null)
        );
        $this->assertEquals(
            'ImageGif_Disseminator Preview',
            $this->diss->preview('image/gif', null)
        );
        $this->assertEquals(
            'XmlTei_Disseminator Preview',
            $this->diss->preview('text/xml', null)
        );
        $this->assertEquals(
            'Default_Disseminator Preview',
            $this->diss->preview('text/unknown', null)
        );
    }

    function testPreviewNull() {
        $this->assertNull(
            $this->diss->preview('text/something', null)
        );
    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
