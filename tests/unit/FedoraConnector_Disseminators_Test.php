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
require_once __DIR__ . '/DatastreamMock.php';


class FedoraConnector_Disseminators_Test extends PHPUnit_Framework_TestCase
{
    var $diss;
    var $mocks;

    function setUp() {
        $this->diss = new FedoraConnector_Disseminators(__DIR__ . '/diss');

        $server = 'http://localhost:8000/';
        $this->mocks = array(
            'text/plain' => new Datastream_Mock('m:0', $server, 'text/plain'),
            'image/gif' => new Datastream_Mock('m:0', $server, 'image/gif'),
            'text/xml' => new Datastream_Mock('m:0', $server, 'text/xml'),
            'text/unknown' => new Datastream_Mock('m:0', $server, 'text/unknown'),
            'text/something' => new Datastream_Mock('m:0', $server, 'text/something'),
            'something/else' => new Datastream_Mock('m:0', $server, 'something/else')
        );
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
            $this->diss->hasDisseminatorFor($this->mocks['text/plain'])
        );
        $this->assertTrue(
            $this->diss->hasDisseminatorFor($this->mocks['image/gif'])
        );
        $this->assertTrue(
            $this->diss->hasDisseminatorFor($this->mocks['text/xml'])
        );
        $this->assertFalse(
            $this->diss->hasDisseminatorFor($this->mocks['something/else'])
        );
    }

    function testGetDisseminator() {
        $this->assertEquals(
            'TextPlain_Disseminator',
            get_class($this->diss->getDisseminator($this->mocks['text/plain']))
        );
        $this->assertEquals(
            'ImageGif_Disseminator',
            get_class($this->diss->getDisseminator($this->mocks['image/gif']))
        );
        $this->assertEquals(
            'XmlTei_Disseminator',
            get_class($this->diss->getDisseminator($this->mocks['text/xml']))
        );
        $this->assertEquals(
            'Default_Disseminator',
            get_class($this->diss->getDisseminator($this->mocks['text/unknown']))
        );
        $this->assertNull(
            $this->diss->getDisseminator($this->mocks['something/else'])
        );
    }

    function testCanHandle() {
        $this->assertTrue(
            $this->diss->canDisplay($this->mocks['text/plain'])
        );
        $this->assertTrue(
            $this->diss->canDisplay($this->mocks['image/gif'])
        );
        $this->assertTrue(
            $this->diss->canDisplay($this->mocks['text/xml'])
        );
        $this->assertTrue(
            $this->diss->canDisplay($this->mocks['text/unknown'])
        );
    }

    function testCanHandleFalse() {
        $this->assertFalse(
            $this->diss->canDisplay($this->mocks['something/else'])
        );
    }

    function testHandle() {
        $this->assertEquals(
            'TextPlain_Disseminator',
            $this->diss->display($this->mocks['text/plain'])
        );
        $this->assertEquals(
            'ImageGif_Disseminator',
            $this->diss->display($this->mocks['image/gif'])
        );
        $this->assertEquals(
            'XmlTei_Disseminator',
            $this->diss->display($this->mocks['text/xml'])
        );
        $this->assertEquals(
            'Default_Disseminator',
            $this->diss->display($this->mocks['text/unknown'])
        );
    }

    function testHandleNull() {
        $this->assertNull(
            $this->diss->display($this->mocks['text/something'])
        );
    }

    function testCanPreview() {
        $this->assertTrue(
            $this->diss->canPreview($this->mocks['text/plain'])
        );
        $this->assertTrue(
            $this->diss->canPreview($this->mocks['image/gif'])
        );
        $this->assertTrue(
            $this->diss->canPreview($this->mocks['text/xml'])
        );
        $this->assertTrue(
            $this->diss->canPreview($this->mocks['text/unknown'])
        );
    }

    function testCanPreviewFalse() {
        $this->assertFalse(
            $this->diss->canPreview($this->mocks['something/else'])
        );
    }

    function testPreview() {
        $this->assertEquals(
            'TextPlain_Disseminator Preview',
            $this->diss->preview($this->mocks['text/plain'])
        );
        $this->assertEquals(
            'ImageGif_Disseminator Preview',
            $this->diss->preview($this->mocks['image/gif'])
        );
        $this->assertEquals(
            'XmlTei_Disseminator Preview',
            $this->diss->preview($this->mocks['text/xml'])
        );
        $this->assertEquals(
            'Default_Disseminator Preview',
            $this->diss->preview($this->mocks['text/unknown'])
        );
    }

    function testPreviewNull() {
        $this->assertNull(
            $this->diss->preview($this->mocks['text/something'])
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
