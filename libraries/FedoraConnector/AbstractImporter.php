<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Abstract importer.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
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
     * Import metadata.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @param string $dsid The dsid to import.
     *
     * @return void
     */
    public function import($object, $dsid)
    {

        // Get object parent item.
        $item = $object->getItem();

        // Get metadata XML for the datastream.
        $xml = new DOMXPath($this->getMetadataXml($object, $dsid));

        // Get DC elements.
        $elements = $this->db->getTable('Element')->findBySet('Dublin Core');

        // Run queries and write texts.
        foreach ($elements as $element) {

            // Execute for the element.
            $queries = $this->getQueries($element->name);
            $nodes = $this->queryAll($xml, $queries);

            // Write new element texts.
            foreach ($nodes as $node) {
                $text = new ElementText;
                $text->record_id = $item->id;
                $text->record_type_id = 2;
                $text->element_id = $element->id;
                $text->html = 0;
                $text->text = $node->nodeValue;
                $text->save();
            }

        }

    }

    /**
     * Execute queries on metadata XML.
     *
     * @param DOMXPath $xml The XML document.
     * @param array $queries The array of queries.
     *
     * @return array $results The node matches.
     */
    public function queryAll($xml, $queries)
    {

        $results = array();

        // Execute queries.
        foreach ($queries as $query) {
            foreach ($xml->query($query) as $node) {
                array_push($results, $node);
            }
        }

        return $results;

    }

    /**
     * Get XML from Fedora for the item.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @param string $dsid The dsid to load.
     *
     * @return DOMDocument The metadata XML.
     */
    public function getMetadataXml($object, $dsid) {
        $url = $object->getMetadataUrl($dsid);
        $xml = new DomDocument();
        $xml->load($url);
        return $xml;
    }

}
