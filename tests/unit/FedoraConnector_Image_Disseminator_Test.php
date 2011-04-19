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


require_once __DIR__ . '/DatastreamMock.php';
require_once __DIR__ . '/../../Disseminators/100-Image.php';


class FedoraConnector_Image_Disseminator_Test extends PHPUnit_Framework_TestCase
{
    var $diss;
    var $ds;

    function setUp() {
        $this->diss = new Image_Disseminator();
        $this->ds = new Datastream_Mock('mock:1234', 'http://localhost:8000/');
    }

    function testCanHandle() {
        $this->assertTrue($this->diss->canHandle('image/jp2', $this->ds));
        $this->assertTrue($this->diss->canHandle('image/jpeg', $this->ds));
        $this->assertTrue($this->diss->canHandle('image/gif', $this->ds));
        $this->assertFalse($this->diss->canHandle('picture/gif', $this->ds));
    }

    function testCanPreview() {
        $this->assertTrue($this->diss->canPreview('image/jp2', $this->ds));
        $this->assertTrue($this->diss->canPreview('image/jpeg', $this->ds));
        $this->assertTrue($this->diss->canPreview('image/gif', $this->ds));
        $this->assertFalse($this->diss->canPreview('picture/gif', $this->ds));
    }

    function testHandle() {
        $this->assertEquals(
            "<img alt='image' src='http://localhost:8000/get/' />",
            $this->diss->handle('image/jp2', $this->ds)
        );
    }

    function testPreview() {
        $this->assertEquals(
            "<img alt='image' src='http://localhost:8000/get/' "
            . "class='fedora-preview' />",
            $this->diss->preview('image/jp2', $this->ds)
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
