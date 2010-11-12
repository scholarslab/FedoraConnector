<?php
/**
 * FedoraConnector_Datastream - each fedora datastream and its relation to fedora pid and Omeka item
 * 
 * @version $Id$ 
 * @package FedoraConnector
 * @author Ethan Gruber
 * @copyright Scholars' Lab
 *
 **/
class FedoraConnector_Datastream extends Omeka_Record { 
	public static function getDatastream($currentPage)
	{
		$db = get_db();
		$et = $db->getTable('FedoraConnector_Datastream');
		$entries = $et->findBy(array('id'=>'id'), 10, $currentPage);
        return $entries;
	}
}