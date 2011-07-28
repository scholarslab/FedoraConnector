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


require_once dirname(__FILE__) . '/DatastreamMock.php';
require_once dirname(__FILE__) . '/../../Renderers/000-Jp2.php';


class FedoraConnector_Jp2_Renderer_Test extends PHPUnit_Framework_TestCase
{
    var $diss;
    var $mocks;

    function setUp() {
        $this->diss = new Jp2_Renderer();
        $server = 'http://localhost:8000/';
        $this->mocks = array(
            'text/plain' => new Datastream_Mock('m:0', $server, 'text/plain'),
            'image/jp2' => new Datastream_Mock('m:0', $server, 'image/jp2'),
            'image/jpeg' => new Datastream_Mock('m:0', $server, 'image/jpeg'),
            'image/gif' => new Datastream_Mock('m:0', $server, 'image/gif'),
            'picture/gif' => new Datastream_Mock('m:0', $server, 'picture/gif'),
            'text/xml' => new Datastream_Mock('m:0', $server, 'text/xml'),
            'text/unknown' => new Datastream_Mock('m:0', $server, 'text/unknown'),
            'text/something' => new Datastream_Mock('m:0', $server, 'text/something'),
            'something/else' => new Datastream_Mock('m:0', $server, 'something/else')
        );
    }

    function testCanDisplay() {
        $this->assertTrue($this->diss->canDisplay($this->mocks['image/jp2']));
        $this->assertFalse($this->diss->canDisplay($this->mocks['image/jpeg']));
    }

    function testCanPreview() {
        $this->assertTrue($this->diss->canPreview($this->mocks['image/jp2']));
        $this->assertFalse($this->diss->canPreview($this->mocks['image/jpeg']));
    }

    function testDisplay() {
        $this->assertEquals(
            "<img alt='image' src='http://localhost:8000/get/m:0/"
            . "djatoka:jp2SDef/getRegion?scale=400,400' />",
            $this->diss->display($this->mocks['image/jp2'])
        );
    }

    function testPreview() {
        $this->assertEquals(
            "<img alt='image' src='http://localhost:8000/get/m:0/"
            . "djatoka:jp2SDef/getRegion?scale=80,80' />",
            $this->diss->preview($this->mocks['image/jp2'])
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
