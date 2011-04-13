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

// XXX change this to some kind of class-based pattern that factors out common 
// code from the functions below.

// ERR: If you refactor this somewhere else, be sure to update
// fedora_connector_list_importers.

/**
 * This imports Dublin Core metadata into the metadata for an object.
 *
 * @param Omeka_Record $datastream This is the data stream to import the
 * metadata for.
 *
 * @return void
 */
function fedora_importer_DC($datastream)
{
	$db = get_db();
    $item = $db
        ->getTable('Item')
        ->find($datastream->item_id);

	// Get the datastream from Fedora REST.
	$dcUrl = fedora_connector_metadata_url($datastream);
	$xml_doc = new DomDocument;
	$xml_doc->load($dcUrl);
	$xpath = new DOMXPath($xml_doc);

    // Get element_ids.
    $dcSetId = $db
        ->getTable('ElementSet')
        ->findByName('Dublin Core')
        ->id;
    $dcElements = $db
        ->getTable('Element')
        ->findBySql('element_set_id = ?', array($dcSetId));

	// Write DC element names and ids to new array for processing.
	$dc = array();
	foreach ($dcElements as $dcElement) {
		$dc[$dcElement['name']] = strtolower($dcElement['name']);
	}

	foreach ($dc as $omekaName => $xmlName) {
		$query = '//*[local-name() = "' . $xmlName . '"]';

		// Get item element texts.
		$element = $item->getElementByNameAndSetName($omekaName, 'Dublin Core');

		// Set element texts for item and file.
		$nodes = $xpath->query($query);
		foreach ($nodes as $node) {
			// addTextForElement not working for some reason...
            $textInfo = array(
                'record_id'      => $item->id,
                'record_type_id' => 2,
                'element_id'     => $element->id,
                'html'           => 0,
                'text'           => $node->nodeValue
            );
            $db->insert('element_texts', $textInfo);
		}
	}
}

/**
 * This imports MODS metadata into the metadata for an object.
 *
 * @param Omeka_Record $datastream This is the data stream to import the
 * metadata for.
 *
 * @return void
 */
function fedora_importer_MODS($datastream) {
	$db = get_db();

	// Get the datastream from Fedora REST.
    $modsUrl = fedora_connector_metadata_url($datastream);
	$xml_doc = new DomDocument;
	$xml_doc->load($modsUrl);
	$xpath = new DOMXPath($xml_doc);

    // Get element_ids.
    $dcSetId = $db
        ->getTable('ElementSet')
        ->findByName('Dublin Core')
        ->id;
    $dcElements = $db
        ->getTable('Element')
        ->findBySql('element_set_id = ?', array($dcSetId));

	// Write DC element names and ids to new array for processing.
	$dc = array();
	foreach ($dcElements as $dcElement) {
        array_push($dc, $dcElement['name']);
	}

	// Map MODS to DC.
    // Based on Library of Congress guidelines:
    // http://www.loc.gov/standards/mods//dcsimple-mods.html
	foreach ($dc as $name) {
        switch ($name) {
        case 'Title':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="titleInfo"]'
                . '/*[local-name()="title"]'
            );
            break;

        case 'Creator':
            $queries = array(
                '//*[local-name()="mods"]'
                . '/*[local-name()="name"][*[local-name()="role"] = "creator"]'
            );
            break;

        case 'Subject':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="subject"]'
                . '/*[local-name()="topic"]'
            );
            break;

        case 'Description':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="abstract"]',
                '//*[local-name()="mods"]/*[local-name()="note"]',
                '//*[local-name()="mods"]/*[local-name()="tableOfContents"]'
            );
            break;

        case 'Publisher':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="originInfo"]'
                . '/*[local-name()="publisher"]'
            );
            break;

        case 'Contributor':
            // Mapping from name/namePart to Contributor specifically is 
            // difficult.  There are likely institutional differences in 
            // mapping.
			$queries = array();
            break;

        case 'Date':
            $prefix = '//*[local-name()="mods"]/*[local-name()="originInfo"]';
            $queries = array(
                $prefix . '/*[local-name()="dateIssued"]',
                $prefix . '/*[local-name()="dateCreated"]',
                $prefix . '/*[local-name()="dateCaptured"]',
                $prefix . '/*[local-name()="dateOther"]'
            );
            break;

        case 'Type':
            // XXX: Originally, this set $queries to something sane and
            // immediately set it again to an empty array.  I need to test to
            // make sure I'm using the right one.
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="typeOfResource"]',
                '//*[local-name()="mods"]/*[local-name()="genre"]'
            );
            break;

        case 'Format':
            // XXX: Originally, this set $queries to something sane and
            // immediately set it again to an empty array.  I need to test to
            // make sure I'm using the right one.
            $prefix
                = '//*[local-name()="mods"]'
                . '/*[local-name()="physicalDescription"]';
            $queries = array(
                $prefix . '/*[local-name()="internetMediaType"]',
                $prefix . '/*[local-name()="extent"]',
                $prefix . '/*[local-name()="form"]'
            );
            break;

        case 'Identifier':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="identifier"]',
                '//*[local-name()="mods"]/*[local-name()="location"]'
                . '/*[local-name()="uri"]'
            );
            break;

        case 'Source':
            $prefix
                = '//*[local-name()="mods"]/*[local-name()="relatedItem"]'
                . '[@type="original"]/*';
            $queries = array(
                $prefix .  '[local-name()="titleInfo"]/*[local-name()="title"]',
                $prefix .  '[local-name()="location"]/*[local-name()="url"]'
            );
            break;

        case 'Language':
            $queries = array(
                '//*[local-name()="mods"]/*[local-name()="language"]'
            );
            break;

        case 'Relation':
            $prefix
                = '//*[local-name()="mods"]/*[local-name()="relatedItem"]/*';
            $queries = array(
                $prefix .  '[local-name()="titleInfo"]/*[local-name()="title"]',
                $prefix .  '[local-name()="location"]/*[local-name()="url"]'
            );
            break;

        case 'Coverage':
            $prefix = '//*[local-name()="mods"]/*[local-name()="subject"]/*';
            $queries = array(
                $prefix . '[local-name()="temporal"]',
                $prefix . '[local-name()="geographic"]',
                $prefix . '[local-name()="hierarchicalGeographic"]',
                $prefix . '[local-name()="cartographics"]'
            );
            break;

        case 'Rights':
            $queries == array(
                '//*[local-name()="mods"]/*[local-name()="accessCondition"]'
            );
            break;
		}

		// Get item element texts.
		$ielement = $item->getElementByNameAndSetName($name, 'Dublin Core');
		$ielementTexts = $item->getTextsByElement($ielement);

		// Set element texts for item and file.
		foreach ($queries as $query) {
			$nodes = $xpath->query($query);
			foreach ($nodes as $node) {
				//trip whitespace from nodeValue
				$value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));
				$item->addTextForElement($ielement, $value);
			}
		}
	}
	$item->saveElementTexts();
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
