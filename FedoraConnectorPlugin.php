<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Plugin runner.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class FedoraConnectorPlugin
{

    // Hooks.
    private static $_hooks = array(
        'install',
        'uninstall',
        'before_delete_item',
        'after_save_form_item',
        'admin_theme_header',
        'define_routes',
        'public_append_to_items_show'
    );

    // Filters.
    private static $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main'
    );

    /**
     * Add hooks and filers, get tables.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_db = get_db();
        $this->_datastreams = $this->_db->getTable('FedoraConnectorDatastream');
        self::addHooksAndFilters();
    }

    /**
     * Connect hooks and filters with callbacks.
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

        // Servers.
        $this->_db->query("CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorServer` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `url` tinytext collate utf8_unicode_ci,
            `name` tinytext collate utf8_unicode_ci,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        // Create datastream table.
        $this->_db->query("CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorDatastream` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `item_id` int(10) unsigned,
            `server_id` int(10) unsigned,
            `pid` tinytext collate utf8_unicode_ci,
            `dsids` tinytext collate utf8_unicode_ci,
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

    }

    /**
     * Drop tables, scrubs out Fedora TEI datastreams.
     *
     * @return void
     */
    public function uninstall()
    {
        $this->_db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorDatastream`");
        $this->_db->query("DROP TABLE IF EXISTS `$db->FedoraConnectorServer`");
    }

    /**
     * Add plugin static assets.
     *
     * @param Zend_Controller_Request_Http $request The request.
     *
     * @return void
     */
    public function adminThemeHeader($request)
    {

        if (in_array($request->getModuleName(), array(
          'fedora-connector', 'default'))) {

            // Admin css.
            queue_css('fedora_connector_main');

            // Datastreams dependencies.
            queue_js('vendor/load/load');
            queue_js('load-datastreams');

        }

    }

    /**
     * Register routes.
     *
     * @param object $router Front controller router.
     *
     * @return void
     */
    public function defineRoutes($router)
    {
        $router->addConfig(new Zend_Config_Ini(
            FEDORA_CONNECTOR_PLUGIN_DIR . '/routes.ini', 'routes'
        ));
    }

    /**
     * Add Fedora tab to the Items interface.
     *
     * @param array $tabs An array mapping tab name to HTML for that tab.
     *
     * @return array The $tabs array updated with the Fedora Datastreams tab.
     */
    public function adminItemsFormTabs($tabs)
    {

        // Construct the form, strip the <form> tag.
        $form = new FedoraConnector_Form_Datastream();
        $form->removeDecorator('form');

        // Get the item.
        $item = get_current_item();

        // If the item is saved.
        if (!is_null($item->id)) {

            // Try to get a datastream.
            $datastream = $this->_datastreams->findByItem($item);

            // Populate fields.
            if ($datastream) {
                $form->populate(array(
                    'server' => $datastream->server_id,
                    'pid' => $datastream->pid,
                    'saved-dsids' => $datastream->dsids
                ));
            }
        }

        // Add tab.
        $tabs['Fedora'] = $form;
        return $tabs;

    }

    /**
     * Save/update datastream, do import.
     *
     * @param Item  $item The item.
     * @param array $post The complete $_POST.
     *
     * @return void.
     */
    public function afterSaveFormItem($item, $post)
    {

        // Create or update the datastream.
        $datastream = $this->_datastreams->createOrUpdate(
            $item, (int) $post['server'], $post['pid'], $post['dsids']
        );

        // Import.
        if ((bool) $post['import']) {
            // ** dev: import
        }

    }

    /**
     * Add Fedora tab to admin menu bar.
     *
     * @param array $tabs Array of label => URI.
     *
     * @return array The modified tabs array.
     */
    public function adminNavigationMain($tabs)
    {
        $tabs['Fedora Connector'] = uri('fedora-connector');
        return $tabs;
    }

}
