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


/**
 * This is an abstract base class for implementing importers.
 *
 * Concrete subclasses need to define canImport and getQueries.
 */
abstract class FedoraConnector_BaseImporter
{

    /**
     * This tests whether the concrete class can import metadata from a 
     * datastream.
     *
     * @param Omeka_Record $datastream The datastream to import metadata from.
     *
     * @return bool Whether the class can import metadata from the datastream.
     */
    abstract function canImport($datastream);

    /**
     * This returns a list of XPath queries for finding the nodes in the input
     * data for the given DC name.
     *
     * @param string $name The DC name of the element to find metadata for.
     *
     * @return array An array of XPath queries.
     */
    abstract function getQueries($name);

    /**
     * This gets the target item for the datastream's metadata.
     *
     * @param Omeka_Record $datastream The datastream to import metadata from.
     *
     * @return Omeka_Record The omeka item to populate with metadata.
     */
    function getItem($datastream) {
        $item = get_db()
            ->getTable('Item')
            ->find($datastream->item_id);
        return $item;
    }

    /**
     * This pulls the datastream's metadata from the FedoraCommons repository, 
     * parses it as XML, and returns the XML DOMdocument.
     *
     * @param Omeka_Record $datastream The datastream to import metadata from.
     *
     * @return DOMDocument The DOM document containing the datastream's 
     * metadata.
     */
    function getMetadataXml($datastream) {
        $url = $datastream->getMetadataUrl();
        $xml = new DomDocument();
        $xml->load($url);
        return $xml;
    }

    /**
     * This pulls the Dublin Core names to search for from the database "Dublin 
     * Core" element set.
     *
     * @return array An array of DC elements to search for.
     */
    function getDublinCoreNames() {
        $db = get_db();
        $select = $db
            ->select()
            ->from(array('e' => 'Element'), array('name'))
            ->join(array('es' => 'ElementSet'), 'e.element_set_id=es.id', array())
            ->where('es.name = ?', 'Dublin Core')
            ->order('name');
        $stmt = $db->query($select);

        $names = array();
        foreach ($stmt->fetchAll() as $row) {
            array_push($names, $row['name']);
        }

        return $names;
    }

    /**
     * This performs all queries in the list on the documents and returns the 
     * nodes for all queries as one array.
     *
     * @param DOMXPath $xpath   The XPath object to perform the queries.
     * @param array    $queries The queries to perform.
     *
     * @return array This returns an array of the DOMNodes in the results of 
     * all queries.
     */
    function queryAll($xpath, $queries) {
        $results = array();

        foreach ($queries as $query) {
            foreach ($xpath->query($query) as $node) {
                array_push($results, $node);
            }
        }

        return $results;
    }

    /**
     * This adds the metadata texts to the database.
     *
     * @param Omeka_Record $item    The item containing the metadata.
     * @param Omeka_Record $element The item's metadata element.
     * @param string       $name    The DC name to store the data under.
     * @param string       $value   The metadata value.
     *
     * @return void
     */
    function addMetadata($item, $element, $name, $value) {
        // XXX Where do the hard-coded values below come from?
        $data = array(
            'record_id'      => $item->id,
            'record_type_id' => 2,
            'element_id'     => $element->id,
            'html'           => 0,
            'text'           => $value
        );

        // XXX This should remove any existing values first.
        $db->insert('element_texts', $data);
    }

    /**
     * This handles the entire system.
     *
     * @param Omeka_Record $datastream The datastream to import metadata from.
     *
     * @return void
     */
    function import($datastream) {
        $item = $this->getItem($datastream);
        $xpath = new DOMXPath($this->getMetadataXml($datastream));
        $dcNames = $this->getDublinCoreNames();

        foreach ($dcNames as $dc) {
            $queries = $this->getQueries($dc);
            $element = $item->getElementByNameAndSetName(
                strtolower($dc),
                'Dublin Core'
            );

            foreach ($this->queryAll($xpath, $queries) as $node) {
                $this->addMetadata($item, $element, $dc, $node->nodeValue);
            }
        }
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
