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
?>

<?php

class FedoraConnectorPlugin
{

    private static $_hooks = array(
        'install',
        'uninstall',
        'before_delete_item',
        'admin_theme_header',
        'define_routes',
        'define_acl',
        'config_form',
        'config',
        'after_save_form_item',
        'public_append_to_items_show'
    );

    private static $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main'
    );

    private $_db;

    /**
     * Delete option 'bagit_version' in the _options, drop tables.
     * table.
     *
     * @return void
     */
    public function __construct()
    {

        self::addHooksAndFilters();

    }

    /**
     * Iterate over hooks and filters, define callbacks.
     *
     * @return void
     */
    public function addHooksAndFilters()
    {

        foreach (self::$_hooks as $hookName) {
            $functionName = Inflector::variablize($hookName);
            add_plugin_hook($hookName, array($this, $functionName));
        }

        foreach (self::$_filters as $filterName) {
            $functionName = Inflector::variablize($filterName);
            add_filter($filterName, array($this, $functionName));
        }

    }

    /**
     * Create tables for datastreams and servers, insert place-holder server,
     * set which datastreams should be omitted by default.
     *
     * @return void
     */
    public function install()
    {

        $db = get_db();

        // Create datastream table.
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

        // Create servers table.
        $db->query("
            CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorServer` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `url` tinytext collate utf8_unicode_ci,
                `name` tinytext collate utf8_unicode_ci,
                `is_default` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
            ");

        // Create table for DC defaults for import behaviors.
        $db->query("
            CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorImportSetting` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `element_id` int(10) unsigned NULL,
                `item_id` int(10) unsigned NULL,
                `behavior` ENUM('overwrite', 'stack', 'block'),
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
            ");

        // Insert default localhost server record.
        $db->query("
            INSERT INTO `$db->FedoraConnectorServer` (url, name, is_default)
            VALUES (
                'http://localhost:8080/fedora/',
                'Default Fedora Server',
                1
            )
            ");

        set_option('fedora_connector_omitted_datastreams', 'RELS-EXT,RELS-INT,AUDIT');
        set_option('fedora_connector_default_import_behavior', 'overwrite');

    }

    /**
     * Drop tables, scrubs out Fedora TEI datastreams.
     *
     * @return void
     */
    public function uninstall()
    {

        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorDatastream`");
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorServer`");
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorImportBehaviorDefault`");
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorImportBehaviorItem`");
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorAddToBlankDefault`");
        $db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorAddToBlankItem`");

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
     * On item delete, get rid of datastreams associated with that item.
     *
     * @param Omeka_Record $item The item being deleted.
     *
     * @return void
     */
    public function beforeDeleteItem($item)
    {

        $db = get_db();
        $datastreams = $db
            ->getTable('FedoraConnectorDatastream')
            ->findBySql('item_id = ?', array($item['id']));

        foreach ($datastreams as $datastream){
            $datastream->delete();
        }

    }

    /**
     * Add plugin specific CSS.
     *
     * @param Zend_Controller_Request_Http $request The request for an admin page.
     *
     * @return void
     */
    public function adminThemeHeader($request)
    {

        if (in_array($request->getModuleName(), array('fedora-connector', 'default'))) {
            queue_css('fedora_connector_main');
        }

    }

    /**
     * Wire up the routes in routes.ini.
     *
     * @param object $router Router passed in by the front controller.
     *
     * @return void
     */
    public function defineRoutes($router)
    {

        $router->addConfig(new Zend_Config_Ini(FEDORA_CONNECTOR_PLUGIN_DIR .
            DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));

    }

    /**
     * Establish ACL privilges.
     *
     * @param Omeka_Acl $acl The ACL instance controlling the access list.
     *
     * @return void
     */
    public function defineAcl($acl)
    {

        if (version_compare(OMEKA_VERSION, '2.0-dev', '<')) {
            $serversResource = new Omeka_Acl_Resource('FedoraConnector_Servers');
            $datastreamsResource = new Omeka_Acl_Resource('FedoraConnector_Datastreams');
        } else {
            $serversResource = new Zend_Acl_Resource('FedoraConnector_Servers');
            $datastreamsResource = new Zend_Acl_Resource('FedoraConnector_Datastreams');
        }

        $acl->add($serversResource);
        $acl->add($datastreamsResource);

        $acl->allow('super', 'FedoraConnector_Servers');
        $acl->allow('admin', 'FedoraConnector_Servers');
        $acl->allow('super', 'FedoraConnector_Datastreams');
        $acl->allow('admin', 'FedoraConnector_Datastreams');

    }

    /**
     * Do config form.
     *
     * @return void
     */
    public function configForm()
    {

        include 'forms/config_form.php';

    }

    /**
     * Save config form (omitted datastreams csv).
     *
     * @return void
     */
    public function config()
    {

        set_option('fedora_connector_omitted_datastreams',
            $_POST['fedora_connector_omitted_datastreams']);

    }

    /**
     * Add Fedora Datastreams tab to the Items interface.
     *
     * @param array $tabs An array mapping tab name to HTML for that tab.
     *
     * @return array The $tabs array updated with the Fedora Datastreams tab.
     */
    public function adminItemsFormTabs($tabs)
    {

        $item = get_current_item();
        if (isset($item->added)) {
            $tabs['Fedora Datastreams'] = fedorahelpers_doItemFedoraForm($item);
            $tabs['Fedora Import Settings'] = fedorahelpers_doItemFedoraImportSettings($item);
        }

        return $tabs;

    }

    /**
     * Add link to main admin menu bar.
     *
     * @param array $tabs This is an array of label => URI pairs.
     *
     * @return array The tabs array passed in with Fedora Connector links possibly
     * added.
     */
    public function adminNavigationMain($tabs)
    {

        if (has_permission('FedoraConnector_Servers', 'index')) {
            $tabs['Fedora Connector'] = uri('fedora-connector/servers');
        }

        return $tabs;

    }

    /**
     * Render the datastream in the public view of the item.
     *
     * @return void.
     */
    public function publicAppendToItemsShow()
    {

        $db = get_db();
        $item = get_current_item();
        $datastreams = $db->getTable('FedoraConnectorDatastream')
            ->findBySql('item_id = ?', array($item->id));

        foreach ($datastreams as $datastream) {
            $renderer = new FedoraConnector_Render;
            echo $renderer->display($datastream);
        }

    }

    /**
     * Lock in changes made to item-specific import settings.
     *
     * @param $record Omeka_record the record.
     * @param $post posted data from the form.
     *
     * @return void
     */
    public function afterSaveFormItem($record, $post)
    {

        $db = get_db();
        foreach ($post['behavior'] as $field => $behavior) {

            // Query for the behavior record and the DC element.
            $behaviorRecord = $db->getTable('FedoraConnectorImportSetting')
                ->getItemBehaviorByField($record, $field);
            $dcElement = $db->getTable('Element')
                ->findByElementSetNameAndElementName('Dublin Core', $field);

            if ($behavior != 'default') {

                // If the record exists, update it.
                if ($behaviorRecord != false) {
                    $behaviorRecord->behavior = $behavior;
                    $behaviorRecord->save();
                }

                // Otherwise, create a new record.
                else {
                    $newBehaviorRecord = new FedoraConnectorImportSetting;
                    $newBehaviorRecord->behavior = $behavior;
                    $newBehaviorRecord->element_id = $dcElement->id;
                    $newBehaviorRecord->item_id = $record->id;
                    $newBehaviorRecord->save();
                }

            }

            else {

                // If the record exists, delete it.
                if ($behaviorRecord != false) {
                    $behaviorRecord->delete();
                }

            }

        }

        $itemDefault = $db->getTable('FedoraConnectorImportSetting')
            ->getItemDefault($record);

        // Update item default.
        if ($post['behavior_default'] != 'default') {

            // If the record exists, update it.
            if ($itemDefault != false) {
                $itemDefault->behavior = $post['behavior_default'];
                $itemDefault->save();
            }

            // Otherwise, create a new record.
            else {
                $itemDefault = new FedoraConnectorImportSetting;
                $itemDefault->behavior = $post['behavior_default'];
                $itemDefault->item_id = $record->id;
                $itemDefault->save();
            }

        }

        else {

            // If the record exists, delete it.
            if ($itemDefault != false) {
                $itemDefault->delete();
            }

        }

    }

}
