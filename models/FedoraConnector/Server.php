<?php
/**
 * FedoraConnector_Server - fedora server urls, human-readable names, and boolean default value
 * 
 * @version $Id$ 
 * @package FedoraConnector
 * @author Ethan Gruber
 * @copyright Scholars' Lab
 *
 **/
class FedoraConnector_Server extends Omeka_Record { 
	public static function getServer($currentPage)
	{
		$db = get_db();
		$et = $db->getTable('FedoraConnector_Server');
		$entries = $et->findBy(array('id'=>'id'), 10, $currentPage);
        return $entries;
	}
}