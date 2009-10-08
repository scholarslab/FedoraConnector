<?php

/**
 * @version $Id$
 * @copyright UVaLib DRS R & D, 2009
 * @package Fedora
 **/

define('FEDORA_PLUGIN_VERSION', get_plugin_ini('Fedora', 'version'));
define('FEDORA_PLUGIN_DIR', dirname(__FILE__));

define('FEDORA_REPO_ROOT', 'http://localhost:8080/fedora');

add_plugin_hook('install', 'fedora_install');
add_plugin_hook('uninstall', 'fedora_uninstall');
add_plugin_hook('before_save_item','fedora_item_to_object');
add_plugin_hook('define_routes', 'fedora_routes');

function fedora_install()  {
	set_option('fedora_version', FEDORA_PLUGIN_VERSION);
}

function fedora_uninstall() {

}

/**
 * Add the routes from routes.ini in this plugin folder.
 *
 * @return void
 **/
function fedora_routes($router) {
	$router->addConfig(new Zend_Config_Ini(FEDORA_PLUGIN_DIR .
	DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
}

function fedora_item_to_object($item) {
	$logger = Omeka_Context::getInstance()->getLogger();
	
	if ( $myitem->getItemType()->name != 'Fedora object') return;
	$identifiers = $myitem->getElementTextsByElementNameAndSetName( 'Fedora PID', 'Item Type Metadata');
	$pid = $identifiers[0]->text;
	$members = getMembers($pid);
	$logger->log($members);
}