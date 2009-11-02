<?php

/**
 * @version $Id$
 * @copyright UVaLib DRS R & D, 2009
 * @package Fedora
 **/

define('FEDORA_CONNECTOR_PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
define('FEDORA_CONNECTOR_PLUGIN_DIR', dirname(__FILE__));

define('FEDORA_REPO_ROOT', 'http://localhost:8080/fedora');

add_plugin_hook('install', 'fedora_connector_install');
add_plugin_hook('uninstall', 'fedora_connector_uninstall');
add_plugin_hook('before_save_item','fedora_connector_item_to_object');
add_plugin_hook('define_routes', 'fedora_connector_routes');

function fedora_connector_install()  {
	set_option('fedora_connector_version', FEDORA_CONNECTOR_PLUGIN_VERSION);
}

function fedora_connector_uninstall() {

}

/**
 * Add the routes from routes.ini in this plugin folder.
 *
 * @return void
 **/
function fedora_connector_routes($router) {
	$router->addConfig(new Zend_Config_Ini(FEDORA_CONNECTOR_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}

function fedora_connector_item_to_object($item) {
	$logger = Omeka_Context::getInstance()->getLogger();
	
	if ( $myitem->getItemType()->name != 'Fedora object') return;
	$identifiers = $myitem->getElementTextsByElementNameAndSetName( 'Fedora PID', 'Item Type Metadata');
	$pid = $identifiers[0]->text;
	$members = getMembers($pid);
	$logger->log($members);
}