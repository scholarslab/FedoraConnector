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

require_once dirname(__FILE__) . '/../../libraries/FedoraConnector/AbstractImporter.php';
require_once dirname(__FILE__) . '/DatastreamMock.php';


class MockItem
{
    var $id;

    function __construct($id) {
        $this->id = $id;
    }

    function getElementByNameAndSetName($name, $setName) {
        return $this;
    }
}

/**
 * This defines the abstract methods and redefines the input/output methods so 
 * I can test the rest.
 */
class TestMockImporter extends FedoraConnector_AbstractImporter
{
    var $item;
    var $dc;
    var $input;
    var $output;

    function __construct($itemId) {
        $this->item = new MockItem($itemId);

        $this->dc = array(
            'Contributor',
            'Coverage',
            'Creator',
            'Date',
            'Description',
            'Format',
            'Identifier',
            'Language',
            'Publisher',
            'Relation',
            'Rights',
            'Source',
            'Subject',
            'Title',
            'Type',
        );

        $this->input = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<metadata>
    <cont>Contributor</cont>
    <cove>Coverage</cove>
    <crea>Creator</crea>
    <date>Date</date>
    <desc>Description</desc>
    <form>Format</form>
    <iden>Identifier</iden>
    <lang>Language</lang>
    <publ>Publisher</publ>
    <rela>Relation</rela>
    <righ>Rights</righ>
    <sour>Source</sour>
    <subj>Subject</subj>
    <titl>Title</titl>
    <type>Type</type>
</metadata>
EOF;

        $this->output = array();

    }

    function getItem($datastream) {
        return $this->item;
    }

    function getMetadataXml($datastream) {
        $xml = new DOMDocument();
        $xml->loadXML($this->input);
        return $xml;
    }

    function getDublinCoreNames() {
        return $this->dc;
    }

    function addMetadata($item, $element, $name, $value) {
        $md = array(
            'item'    => $item,
            'element' => $element,
            'name'    => $name,
            'value'   => $value
        );
        array_push($this->output, $md);
    }

    function canImport($datastream) {
        return true;
    }

    function getQueries($name) {
        $query = '//*[local-name()="' . substr(strtolower($name), 0, 4) . '"]';
        return array($query);
    }

}


class FedoraConnector_BaseImporter_Test extends PHPUnit_Framework_TestCase
{
    var $imp;
    var $ds;

    function setUp() {
        $this->imp = new TestMockImporter(42);
        $this->ds = new Datastream_Mock(
            'm:000',
            'http://localhost:8000/',
            'text/xml',
            'DC',
            'DC'
        );
    }

    private function _runQueryAll($queries) {
        $xpath = new DOMXPath($this->imp->getMetadataXml($this->ds));
        $results = $this->imp->queryAll($xpath, $queries);
        return $results;
    }

    function testQueryAllOneHit() {
        $results = $this->_runQueryAll(array('//cont'));
        $this->assertEquals(1, count($results));
        $this->assertEquals('cont', $results[0]->nodeName);
    }

    function testQueryAllManyHits() {
        $results = $this->_runQueryAll(array(
            '//*[substring(local-name(), 1, 1) = "c"]',
            '//*[substring(local-name(), 1, 1) = "d"]'
        ));

        $this->assertEquals(5, count($results));

        $this->assertEquals('cont', $results[0]->nodeName);
        $this->assertEquals('cove', $results[1]->nodeName);
        $this->assertEquals('crea', $results[2]->nodeName);
        $this->assertEquals('date', $results[3]->nodeName);
        $this->assertEquals('desc', $results[4]->nodeName);
    }

    function testQueryAllNoHits() {
        $results = $this->_runQueryAll(array(
            '//*[substring(local-name(), 1, 1) = "q"]',
            '//*[substring(local-name(), 1, 1) = "x"]'
        ));
        $this->assertEquals(0, count($results));
    }

    // function testImport() {
    //     $this->imp->import($this->ds);

    //     $this->assertTrue(count($this->imp->output) > 0);
    //     foreach ($this->imp->output as $out) {
    //         $this->assertEquals($out['name'], $out['value']);
    //     }
    // }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
