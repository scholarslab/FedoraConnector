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
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

//include the importers which are stored in separate files
// require_once "Importers.php";
// require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/fedora_utils.php';
// require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/pid_form.php';

// {{{ constants
define('FEDORA_CONNECTOR_PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
define('FEDORA_CONNECTOR_PLUGIN_DIR', dirname(__FILE__));
// }}}

// {{{ plugin_hooks
add_plugin_hook('install', 'fedora_connector_install');
add_plugin_hook('uninstall', 'fedora_connector_uninstall');
add_plugin_hook('before_delete_item', 'fedora_connector_before_delete_item');
add_plugin_hook('admin_theme_header', 'fedora_connector_admin_header');
add_plugin_hook('define_acl', 'fedora_connector_define_acl');
add_plugin_hook('config_form', 'fedora_connector_config_form');
add_plugin_hook('config', 'fedora_connector_config');
// }}}

// {{{ filters
add_filter('admin_items_form_tabs', 'fedora_connector_item_form_tabs');
add_filter('admin_navigation_main', 'fedora_connector_admin_navigation');
// }}}

/**
 * Create tables for datastreams and servers, insert place-holder server,
 * set which datastreams should be omitted by default.
 *
 * @return void
 */
function fedora_connector_install()
{

    $db = get_db();

    $db->query("
        CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorDatastream` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `item_id` int(10) unsigned,
            `server_id` int(10) unsigned,
            `pid` tinytext collate utf8_unicode_ci,
            `datastream` tinytext collate utf8_unicode_ci,
            `mime_type` tinytext collate utf8_unicode_ci,
            `metadata_stream` tinytext collate utf8_unicode_ci,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");

    $db->query("
        CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorServer` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `url` tinytext collate utf8_unicode_ci,
            `name` tinytext collate utf8_unicode_ci,
            `version` tinytext collate utf8_unicode_ci,
            `is_default` tinyint(1) unsigned NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");

    $db->query("
        INSERT INTO `$db->FedoraConnectorServer` (url, name, is_default)
        VALUES (
            'http://localhost:8080/fedora/',
            'Default Fedora Server',
            1
        )
        ");

    set_option('fedora_connector_omitted_datastreams', 'RELS-EXT,RELS-INT,AUDIT');

}

/**
 * This uninstalls the Fedora Connector plugin.
 *
 * This drops the server and datastream tables and, if the TeiDisplay plugin is
 * installed, it removes all Fedora TEI datastreams from it.
 *
 * @return void
 */
function fedora_connector_uninstall() {


    $db = get_db();
    $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorDatastream`");
    $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorServer`");


    // If TeiDisplay is installed, remove Fedora TEI datastreams from its 
    // table.
    //
    // XXX: Test that this function doesn't exist whenever the TeiDisplay 
    // plugin directory is in omeka/plugins. Make sure that it only exists 
    // whenever the plugin has been installed.
    if (function_exists('tei_display_installed')) {
        $teiFiles = $db
            ->getTable('TeiDisplay_Config')
            ->findBySql('is_fedora_datastream = ?', array(1));

        foreach ($teiFiles as $teiFile) {
            $teiFile->delete();
        }
    }
}

/**
 * This removes all datastreams associated with an item.
 *
 * @param Omeka_Record $item The item model instance being deleted.
 *
 * @return void
 */
function fedora_connector_before_delete_item($item)
{

    $db = get_db();
    $datastreams = $db
        ->getTable('FedoraConnector_Datastream')
        ->findBySql('item_id = ?', array($item['id']));

    foreach ($datastreams as $datastream){
        $datastream->delete();
    }

}

/**
 * This loads fedora_connector_main.css when the Fedora Connector module is
 * active.
 *
 * @param Zend_Controller_Request_Http $request The request for an admin page.
 *
 * @return void
 */
function fedora_connector_admin_header($request)
{

    if ($request->getModuleName() == 'fedora-connector') {
        queue_css('fedora_connector_main');
    }

}

/**
 * This controls access to the Fedora Connector resources (server and 
 * datastreams).
 *
 * @param Omeka_Acl $acl The ACL instance controlling the access list.
 *
 * @return void
 */
function fedora_connector_define_acl($acl)
{
    $acl->loadResourceList(
        array('FedoraConnector_Server' => array('index', 'status'))
    );
    $acl->loadResourceList(
        array('FedoraConnector_Datastream' => array('index', 'status'))
    );
}

/**
 * This adds Fedora Connector links to the top-level navigation in the admin 
 * theme.
 *
 * @param array $tabs This is an array of label => URI pairs.
 *
 * @return array The tabs array passed in with Fedora Connector links possibly 
 * added.
 */
function fedora_connector_admin_navigation($tabs)
{
    $acl = get_acl();

    if ($acl->checkUserPermission('FedoraConnector_Server', 'index')) {
        $tabs['Fedora Servers'] = uri('fedora-connector/servers/');
    }
    if ($acl->checkUserPermission('FedoraConnector_Datastream', 'browse')) {
        $tabs['Fedora Objects'] = uri('fedora-connector/datastreams/browse/');
    }

    return $tabs;
}

/**
 * This outputs the field for the configuration form.
 *
 * @return void
 */
function fedora_connector_config_form()
{

    include 'config_form.php';

}

/**
 * This processes the config form, saving the input into the options.
 *
 * @return void
 */
function fedora_connector_config()
{

    set_option('fedora_connector_omitted_datastreams',
        $_POST['fedora_connector_omitted_datastreams']);

}

/**
 * This adds Fedora Datastreams tab to Edit Items form page.
 *
 * @param array $tabs An array mapping tab name to HTML for that tab.
 *
 * @return array The $tabs array updated with the Fedora Datastreams tab.
 */
function fedora_connector_item_form_tabs($tabs)
{
   // Insert the tab before the Tags tab.
   // $item = get_current_item();
   // $ttabs = array();
   // foreach($tabs as $key => $html) {
   //     if ($key == 'Tags') {
   //         $ttabs['Fedora Datastreams'] = fedora_connector_pid_form($item);
   //     }
   //     $ttabs[$key] = $html;
   // }
   // $tabs = $ttabs;
   // return $tabs;

    $tabs['Fedora Datastreams'] = ' ';
    return $tabs;
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
