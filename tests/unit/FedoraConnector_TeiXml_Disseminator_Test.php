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
require_once __DIR__ . '/../../Disseminators/200-TeiXml.php';

if (! function_exists('tei_display_installed')) {
    require_once __DIR__ . '/tei_display_mock.php';
}


class FedoraConnector_TeiXml_Disseminator_Test extends PHPUnit_Framework_TestCase
{
    var $diss;
    var $jpeg;
    var $xml;

    function setUp() {
        $this->diss = new TeiXml_Disseminator();
        $this->jpeg = new Datastream_Mock(
            'mock:1234',
            'http://localhost:8000/',
            'image/jpeg',
            'TEI'
        );
        $this->xml = new Datastream_Mock(
            'mock:1234',
            'http://localhost:8000/',
            'text/xml',
            'TEI'
        );
    }

    function testCanDisplay() {
        $this->assertFalse($this->diss->canDisplay($this->jpeg));
        $this->assertTrue($this->diss->canDisplay($this->xml));

        $this->xml->datastream = 'Other';
        $this->assertFalse($this->diss->canDisplay($this->xml));
    }

    function testCanPreview() {
        $this->assertFalse($this->diss->canPreview($this->jpeg));
        $this->assertFalse($this->diss->canPreview($this->xml));

        $this->xml->datastream = 'Other';
        $this->assertFalse($this->diss->canPreview($this->xml));
    }

    /*
     * Since this really just shells out to TeiDisplay, and since it appears 
     * that it will try to pull the document from a Fedora server, I'm not 
     * setting all that up to test this.
     *
    function testDisplay() {
        $this->assertEquals(
            "<img alt='image' src='http://localhost:8000/get/mock:1234/"
            . "djatoka:jp2SDef/getRegion?scale=400,400' />",
            $this->diss->display('image/jp2', $this->ds)
        );
    }
     */

    function testPreview() {
        $this->assertEquals('', $this->diss->preview($this->xml));
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
