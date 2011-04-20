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

require_once __DIR__ . '/../../libraries/FedoraConnector/Importers.php';
require_once __DIR__ . '/DatastreamMock.php';


class FedoraConnector_Importers_Test extends PHPUnit_Framework_TestCase
{
    var $imps;
    var $mocks;

    function setUp() {
        $this->imps = new FedoraConnector_Importers();
        $this->mocks = array(
            'DC' => new Datastream_Mock(
                'mock:000', 'http://localhost:8000/', 'text/xml', 'XML', 'DC'
            ),
            'MODS' => new Datastream_Mock(
                'mock:001', 'http://localhost:8000/', 'text/xml', 'XML', 'MODS'
            ),
            'NULL' => new Datastream_Mock(
                'mock:001', 'http://localhost:8000/', 'text/xml', 'XML', 'NULL'
            )
        );
    }

    function testGetImporters() {
        $impList = $this->imps->getImporters();
        $this->assertEquals(2, count($impList));

        $expected = array(
            'DC_Importer',
            'MODS_Importer'
        );

        for ($i=0; $i<count($impList); $i++) {
            $imps = $impList[$i];
            $this->assertEquals($expected[$i], get_class($imps));
        }
    }

    function testGetImporter() {
        $this->assertEquals(
            'DC_Importer',
            get_class($this->imps->getImporter($this->mocks['DC']))
        );
        $this->assertEquals(
            'MODS_Importer',
            get_class($this->imps->getImporter($this->mocks['MODS']))
        );
        $this->assertNull(
            $this->imps->getImporter($this->mocks['NULL'])
        );
    }

    function testCanImport() {
        $this->assertTrue($this->imps->canImport($this->mocks['DC']));
        $this->assertTrue($this->imps->canImport($this->mocks['MODS']));
    }

    function testCanImportFalse() {
        $this->assertFalse($this->imps->canImport($this->mocks['NULL']));
    }

    /* ->import(...) is tested by the the tests for PluginDir and the importers 
     * themselves.
    function testImport() {
        $this->assertEquals(
            'DC_Importer',
            $this->imps->import('DC', null)
        );
        $this->assertEquals(
            'MODS_Importer',
            $this->imps->import('MODS', null)
        );
    }

    function testImportNull() {
        $this->assertNull(
            $this->imps->import('text/something', null)
        );
    }
     */

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
