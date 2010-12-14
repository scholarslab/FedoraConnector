<?php 

/***************
 * IMPORTERS
 ***************/

//Dublin Core
function fedora_importer_DC($datastream){
	$db = get_db();
	$item = $db->getTable('Item')->find($datastream->item_id);
	$dcUrl = fedora_connector_metadata_url($datastream);

	//get the datastream from Fedora REST
	$xml_doc = new DomDocument;	
	$xml_doc->load($dcUrl);
	$xpath = new DOMXPath($xml_doc);
	
    //get element_ids
	$dcSetId = $db->getTable('ElementSet')->findByName('Dublin Core')->id;
	$dcElements = $db->getTable('Element')->findBySql('element_set_id = ?', array($dcSetId));
	$dc = array();
	
	//write DC element names and ids to new array for processing
	foreach ($dcElements as $dcElement){
		$dc[$dcElement['name']] = strtolower($dcElement['name']);
	}
	
	foreach ($dc as $omekaName=>$xmlName){
		$query = '//*[local-name() = "' . $xmlName . '"]';
		
		//get item element texts
		$element = $item->getElementByNameAndSetName($omekaName, 'Dublin Core');
		
		//set element texts for item and file
		$nodes = $xpath->query($query);
		foreach ($nodes as $node){
			//$item->addTextForElement($element, $node->nodeValue);	
			
			//addTextForElement not working for some reason...
			$db->insert('element_texts', array('record_id'=>$item->id, 'record_type_id'=>2, 'element_id'=>$element->id, 'html'=>0, 'text'=>$node->nodeValue));
					
		}
	}
	return;
}

//MODS
function fedora_importer_MODS($datastream){
	$db = get_db();
    $modsUrl = fedora_connector_metadata_url($datastream);

	//get the datastream from Fedora REST
	$xml_doc = new DomDocument;	
	$xml_doc->load($modsUrl);
	$xpath = new DOMXPath($xml_doc);
	
    //get element_ids
	$dcSetId = $db->getTable('ElementSet')->findByName('Dublin Core')->id;
	$dcElements = $db->getTable('Element')->findBySql('element_set_id = ?', array($dcSetId));
	$dc = array();
	
	//write DC element names and ids to new array for processing
	foreach ($dcElements as $dcElement){
		$dc[] = $dcElement['name'];
	}
	
	//map MODS to DC
	//based on Library of Congress guidelines: http://www.loc.gov/standards/mods//dcsimple-mods.html
	foreach ($dc as $name){
		if ($name == 'Title'){
			$queries = array('//*[local-name()="mods"]/*[local-name()="titleInfo"]/*[local-name()="title"]');
		} elseif ($name == 'Creator'){
			$queries = array('//*[local-name()="mods"]/*[local-name()="name"][*[local-name()="role"] = "creator"]');
		} elseif ($name == 'Subject'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="subject"]/*[local-name()="topic"]');
		} elseif ($name == 'Description'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="abstract"]',
								'//*[local-name()="mods"]/*[local-name()="note"]',
								'//*[local-name()="mods"]/*[local-name()="tableOfContents"]');
		} elseif ($name == 'Publisher'){
			$queries = array('//*[local-name()="mods"]/*[local-name()="originInfo"]/*[local-name()="publisher"]');
		} elseif ($name == 'Contributor'){
			//Mapping from name/namePart to Contributor specifically is difficult.  There are likely institutional differences in mapping
			$queries = array();
		} elseif ($name == 'Date'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="originInfo"]/*[local-name()="dateIssued"]',
								'//*[local-name()="mods"]/*[local-name()="originInfo"]/*[local-name()="dateCreated"]',
								'//*[local-name()="mods"]/*[local-name()="originInfo"]/*[local-name()="dateCaptured"]',
								'//*[local-name()="mods"]/*[local-name()="originInfo"]/*[local-name()="dateOther"]');
		} elseif ($name == 'Type'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="typeOfResource"]',
								'//*[local-name()="mods"]/*[local-name()="genre"]');
			$queries = array();				
		} elseif ($name == 'Format'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="physicalDescription"]/*[local-name()="internetMediaType"]',
								'//*[local-name()="mods"]/*[local-name()="physicalDescription"]/*[local-name()="extent"]',
								'//*[local-name()="mods"]/*[local-name()="physicalDescription"]/*[local-name()="form"]');
			$queries == array();
		} elseif ($name == 'Identifier'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="identifier"]',
								'//*[local-name()="mods"]/*[local-name()="location"]/*[local-name()="uri"]');
		} elseif ($name == 'Source'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="relatedItem"][@type="original"]/*[local-name()="titleInfo"]/*[local-name()="title"]',
								'//*[local-name()="mods"]/*[local-name()="relatedItem"][@type="original"]/*[local-name()="location"]/*[local-name()="url"]');
		} elseif ($name == 'Language'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="language"]');
		} elseif ($name == 'Relation'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="relatedItem"]/*[local-name()="titleInfo"]/*[local-name()="title"]',
								'//*[local-name()="mods"]/*[local-name()="relatedItem"]/*[local-name()="location"]/*[local-name()="url"]');
		} elseif ($name == 'Coverage'){
			$queries = array(	'//*[local-name()="mods"]/*[local-name()="subject"]/*[local-name()="temporal"]',
								'//*[local-name()="mods"]/*[local-name()="subject"]/*[local-name()="geographic"]',
								'//*[local-name()="mods"]/*[local-name()="subject"]/*[local-name()="hierarchicalGeographic"]',
								'//*[local-name()="mods"]/*[local-name()="subject"]/*[local-name()="cartographics"]');
		} elseif ($name == 'Rights'){
			$queries == array('//*[local-name()="mods"]/*[local-name()="accessCondition"]');
		}
		
		//get item element texts
		$ielement = $item->getElementByNameAndSetName($name, 'Dublin Core');
		$ielementTexts = $item->getTextsByElement($ielement);
		
		//set element texts for item and file
		foreach ($queries as $query){
			$nodes = $xpath->query($query);
			foreach ($nodes as $node){					
				//trip whitespace from nodeValue
				$value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));
				$item->addTextForElement($ielement, $value);				
			}
		}
	}
	$item->saveElementTexts();
}