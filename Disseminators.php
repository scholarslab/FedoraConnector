<?php 

/***************
 * DISSEMINATORS 
 ***************/

//image/jpeg
function fedora_disseminator_imagejpeg($datastream,$options){
	$db = get_db();
	$server = fedora_connector_get_server($datastream);
	$url = $server . 'objects/' . $datastream->pid . '/datastreams/' . $datastream->datastream . '/content';
}

//JP2K = image/jp2
function fedora_disseminator_imagejp2 ($datastream,$options){
	$db = get_db();
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
