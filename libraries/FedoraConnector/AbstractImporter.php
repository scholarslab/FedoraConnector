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
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

abstract class FedoraConnector_AbstractImporter
{

    /**
     * Test if the datastream metadata format has an importer plugin.
     *
     * @param Omeka_Record $datastream The datastream to import metadata from.
     *
     * @return bool True if format can be handled.
     */
    abstract function canImport($datastream);

    /**
     * Get xpath queries for finding nodes in the input data for a given DC name.
     *
     * @param string $name The DC name of the element.
     *
     * @return array The array of xpath queries.
     */
    abstract function getQueries($name);

    /**
     * Get database object and set datastream.
     *
     * @return void.
     */
    public function __construct()
    {

        $this->db = get_db();

    }

    /**
     * Dispatcher.
     *
     * @return void
     */
    public function import($datastream)
    {

        $this->datastream = $datastream;

        $item = $this->getItem();
        $xpath = new DOMXPath($this->getMetadataXml());
        $dcNames = $this->getDublinCoreNames();

        // foreach ($dcNames as $name) {
        //     $queries = $this->getQueries($name);
        //     $element = $item->getElementByNameAndSetName(
        //         strtolower($name),
        //         'Dublin Core'
        //     );
        // }

        // // What should be happening here?
        // // $this->clearMetadata($item);

        // foreach ($this->queryAll($xpath, $queries) as $node) {
        //     $this->addMetadata($item, $element, $dc, $node->nodeValue);
        // }

    }

    /**
     * Fetch the target item.
     *
     * @return $item The item.
     */
    public function getItem()
    {

        return $this->db
            ->getTable('Item')
            ->find($this->datastream->id);

    }

    /**
     * Get XML from Fedora for the item.
     *
     * @return DOMDocument The metadata XML.
     */
    public function getMetadataXml() {

        $url = $this->datastream->getMetadataUrl();
        $xml = new DomDocument();
        $xml->load($url);
        return $xml;

    }

    /**
     * Pull DC names to search for.
     *
     * @return array The array of DC elements to search for.
     */
    public function getDublinCoreNames() {

        $select = $this->db
            ->select()
            ->from(array('e' => 'Element'), array('name'))
            ->join(array('es' => 'ElementSet'), 'e.element_set_id = es.id', array())
            ->where('es.name = ?', 'Dublin Core')
            ->order('name');
        $elements = $this->db->fetchObjects($select);

        foreach ($elements as $element) {
            $names[] = $element->name;
        }

        return $names;

    }

    /**
     * Perform all queries in the list on the documents and returns the
     * nodes for all queries as an array.
     *
     * @param DOMXPath $xpath The XPath object to perform the queries.
     * @param array $queries The queries to perform.
     *
     * @return array The resulting array of DOMNodes.
     */
    public function queryAll($xpath, $queries) {

        foreach ($queries as $query) {
            foreach ($xpath->query($query) as $node) {
                $results[] = $node;
            }
        }

        return $results;

    }

    /**
     * Get rid of existing imported metadata for an item.
     *
     * @param Omeka_Record $item The item to scrub.
     *
     * @return void
     */
    public function clearMetadata($item) {

        $texts = $this->db
            ->getTable('ElementText')
            ->findBySql('record_id = ?', array($item->id));

        foreach ($texts as $text) {
            $text->delete();
        }

    }

    /**
     * Adds the metadata texts to the database.
     *
     * @param Omeka_Record $item The item containing the metadata.
     * @param Omeka_Record $element The item's metadata element.
     * @param string $name The DC name to store the data under.
     * @param string $value The metadata value.
     *
     * @return void
     */
    public function addMetadata($item, $element, $name, $value) {

        // Where do the hard-coded values below come from?
        $data = array(
            'record_id'      => $item->id,
            'record_type_id' => 2,
            'element_id'     => $element->id,
            'html'           => 0,
            'text'           => $value
        );

        $db->insert('element_texts', $data);

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
