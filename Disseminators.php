<?php 

/***************
 * DISSEMINATORS 
 ***************/

//image/jpeg
function fedora_disseminator_imagejpeg($datastream,$options){
	$server = fedora_connector_get_server($datastream);
	$url = fedora_connector_content_url($datastream, $server);
	$html = '<img alt="image" src="' . $url . '"/>';
	return $html;
}

//JP2K = image/jp2
function fedora_disseminator_imagejp2($datastream,$options){
	$server = fedora_connector_get_server($datastream);	
	$size = $options['size'];
	$url = $server . 'get/' . $datastream->pid . '/djatoka:jp2SDef/getRegion';
	switch($size){
			case 'thumb':
				$html = '<img alt="image" src="' . $url . '?scale=120,120"/>';
				break;
			case 'screen':
				$html = '<img alt="image" src="' . $url . '?scale=600,600"/>';
				break;
			default:
				$html = '<img alt="image" src="' . $url . '?scale=400,400"/>';
		}	
	return $html;
}

//TEI (Text Encoding Initiative) text/xml
// REQUIRES TeiDisplay Plugin
// Uncomment if TeiDisplay plugin is installed
function fedora_disseminator_tei($datastream,$options){
	$db = get_db();
	$teiFiles = $db->getTable('TeiDisplay_Config')->findbySql('item_id = ?', array($datastream->item_id));
	foreach ($teiFiles as $teiFile){
		echo render_tei_file($teiFile->id, $_GET['section']);
	}
	return;
}