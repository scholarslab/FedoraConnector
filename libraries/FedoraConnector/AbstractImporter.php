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

        $item = $this->getItem($datastream);
        $xpath = new DOMXPath($this->getMetadataXml($datastream));
        $dcNames = $this->getDublinCoreNames();

        foreach ($dcNames as $name) {

            $queries = $this->getQueries($name);
            $element = $item->getElementByNameAndSetName(
                $name,
                'Dublin Core'
            );

            $behavior = $this->db
                ->getTable('FedoraConnectorImportSetting')->getBehavior($element, $item);

            // foreach ($this->queryAll($xpath, $queries) as $node) {
                $this->addMetadata(
                    $item,
                    $element,
                    $name,
                    // $node->nodeValue,
                    $behavior,
                    $this->queryAll($xpath, $queries)
                );
            // }

        }

    }

    /**
     * Fetch the target item.
     *
     * @return $item The item.
     */
    public function queryAll($xpath, $queries)
    {

        $results = array();

        foreach ($queries as $query) {
            foreach ($xpath->query($query) as $node) {
                array_push($results, $node);
            }
        }

        return $results;

    }

    /**
     * Fetch the target item.
     *
     * @return $item The item.
     */
    public function getItem($datastream)
    {

        return $this->db
            ->getTable('Item')
            ->find($datastream->item_id);

    }

    /**
     * Get XML from Fedora for the item.
     *
     * @return DOMDocument The metadata XML.
     */
    public function getMetadataXml($datastream) {

        $url = $datastream->getMetadataUrl();
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
            ->from(array('e' => $this->db->prefix . 'elements'), array('name'))
            ->join(array('es' => $this->db->prefix . 'element_sets'), 'e.element_set_id = es.id', array())
            ->where('es.name = ?', 'Dublin Core')
            ->order('name');

        $elements = $this->db->query($select);

        foreach ($elements->fetchAll() as $element) {
            $names[] = $element['name'];
        }

        return $names;

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
    public function addMetadata($item, $element, $name, $behavior, $nodes) {

        // Look for existing element texts.
        $select = $this->db->getTable('ElementText')->getSelect()
            ->where('element_id = ' . $element->id . ' AND record_id = ' . $item->id);

        $existingTexts = $this->db->getTable('ElementText')->fetchObjects($select);

        switch ($behavior) {

            case 'overwrite':

                // First, delete existing texts.
                foreach ($existingTexts as $text) {
                    $text->delete();
                }

                // Then, add the new ones.
                foreach ($nodes as $node) {

                    $text = new ElementText;
                    $text->record_id = $item->id;
                    $text->record_type_id = 2;
                    $text->element_id = $element->id;
                    $text->html = 0;
                    $text->text = $node->nodeValue;
                    $text->save();

                }

            break;

            case 'stack':

                // Add the new nodes, without deleting the old ones.
                foreach ($nodes as $node) {

                    $text = new ElementText;
                    $text->record_id = $item->id;
                    $text->record_type_id = 2;
                    $text->element_id = $element->id;
                    $text->html = 0;
                    $text->text = $node->nodeValue;
                    $text->save();

                }

            break;

            case 'block':

                return;

            break;

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
