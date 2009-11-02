<?php
include_once('Zend/Loader.php');
Zend_Loader::loadClass('Zend_Soap_Client');

function getMembers($pid) {
	$client = new Zend_Soap_Client(FEDORA_CONNECTOR_REPO_ROOT."/services/accessS?wsdl", array('encoding' => 'UTF-8'));	
	$datastreams = $client->listDatastreams( array('pid' => $pid) );
	return $datastreams;
}
