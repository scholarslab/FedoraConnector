<?php 

/***************
 * IMPORTERS
 ***************/

//Dublin Core
function fedora_importer_DC($datastream){
	$db = get_db();
	$item = $db->getTable('Item')->find($datastream->item_id);
    $server = fedora_connector_get_server($datastream);
    $dcUrl = $server . 'objects/' . $datastream->pid . '/datastreams/' . $datastream->metadata_stream . '/content';

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
	$item = $db->getTable('Item')->find($datastream->item_id);
    $server = fedora_connector_get_server($datastream);
    $dcUrl = $server . 'objects/' . $datastream->pid . '/datastreams/' . $datastream->metadata_stream . '/content';

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
		$dc[] = $dcElement['name'];
	}
	
	//map MODS to DC
	//based on Library of Congress guidelines: http://www.loc.gov/standards/mods//dcsimple-mods.html
	foreach ($dc as $name){
		if ($name == 'Title'){
			$queries = array('//*[local-name()="titleInfo"]/*[local-name()="title"]');
		} elseif ($name == 'Creator'){
			$queries = array('//*[local-name()="name"][*[local-name()="role"] = "creator"]');
		}/* elseif ($name == 'Subject'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="textClass"]/*[local-name()="keywords"]/*[local-name()="list"]/*[local-name()="item"]');
		} elseif ($name == 'Description'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="refsDecl"]',
								'//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="projectDesc"]',
								'//*[local-name()="teiHeader"]/*[local-name()="encodingDesc"]/*[local-name()="editorialDesc"]');
		} elseif ($name == 'Publisher'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]/*[local-name()="publisher"]',
								'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]/*[local-name()="pubPlace"]');
		} elseif ($name == 'Contributor'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="editor"]',
								'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="funder"]',
								'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="sponsor"]',
								'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="titleStmt"]/*[local-name()="principle"]');
		} elseif ($name == 'Date'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="date"]');
		} elseif ($name == 'Type'){
			//type, defined with Item Type Metadata dropdown
			$queries = array();				
		} elseif ($name == 'Format'){
			//format, added manually as text/*[local-name()="xml"] below
			$queries == array();
		} elseif ($name == 'Identifier'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="idno"][@type="ARK"]');
		} elseif ($name == 'Source'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="sourceDesc"]/*[local-name()="bibful"]/*[local-name()="publicationStmt"]/*[local-name()="publisher"]',
								'//*[local-name()="teiHeader"]/*[local-name()="sourceDesc"]/*[local-name()="bibful"]/*[local-name()="publicationStmt"]/*[local-name()="pubPlace"]',
								'//*[local-name()="teiHeader"]/*[local-name()="sourceDesc"]/*[local-name()="bibful"]/*[local-name()="publicationStmt"]/*[local-name()="date"]',
								'//*[local-name()="teiHeader"]/*[local-name()="sourceDesc"]/*[local-name()="bibl"]');
		} elseif ($name == 'Language'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="profileDesc"]/*[local-name()="langUsage"]/*[local-name()="language"]');
		} elseif ($name == 'Relation'){
			$queries = array(	'//*[local-name()="teiHeader"]/*[local-name()="fileDesc"]/*[local-name()="seriesStmt"]/*[local-name()="title"]');
		} elseif ($name == 'Coverage'){
			//skip coverage, there is no clear mapping from TEI Header to Dublin Core
			$queries == array();
		} elseif ($name == 'Rights'){
			$queries == array('//*[local-name()="teiheader"]/*[local-name()="fileDesc"]/*[local-name()="publicationStmt"]/*[local-name()="availability"]');
		}*/
		
		//get item element texts
		$ielement = $item->getElementByNameAndSetName($name, 'Dublin Core');
		$ielementTexts = $item->getTextsByElement($ielement);
		$itexts = array();
		foreach ($ielementTexts as $ielementText){
			$itexts[] = $ielementText['text'];
		}
		
		//set element texts for item and file
		foreach ($queries as $query){
			$nodes = $xpath->query($query);
			foreach ($nodes as $node){					
				//see if that text is already set and don't put in any blank or null fields
				$value = preg_replace('/\s\s+/', ' ', trim($node->nodeValue));
				
				//item
				if (!in_array(trim($value), $itexts) && trim($value) != '' && trim($value) != NULL){
					$item->addTextForElement($ielement, trim($value));
				}
			}
		}
		
		$item->saveElementTexts();
	}
}