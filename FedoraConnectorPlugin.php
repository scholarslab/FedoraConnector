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

    private static $_hooks = array(
        'install',
        'uninstall',
        'before_delete_item',
        'after_save_form_item',
        'admin_theme_header',
        'define_routes',
        'config_form',
        'config',
        'public_append_to_items_show'
    );

    private static $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main'
    );

    private $_db;

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

        // Create servers table.
        $db->query("
          CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorServer` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `url` tinytext collate utf8_unicode_ci,
            `name` tinytext collate utf8_unicode_ci,
            PRIMARY KEY  (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");

        // Create datastream table.
        $db->query("
          CREATE TABLE IF NOT EXISTS `$db->FedoraConnectorDatastream` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `item_id` int(10) unsigned,
            `server_id` int(10) unsigned,
            `pid` tinytext collate utf8_unicode_ci,
            `dsid` tinytext collate utf8_unicode_ci,
            PRIMARY KEY  (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");

        set_option(
          'fedora_connector_omitted_datastreams',
          'RELS-EXT,RELS-INT,AUDIT'
        );

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
     * Wire up the routes in routes.ini.
     *
     * @param object $router Router passed in by the front controller.
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
     * Do config form.
     *
     * @return void
     */
    public function configForm()
    {
        include 'forms/ConfigForm.php';
    }

    /**
     * Save config form (omitted datastreams csv).
     *
     * @return void
     */
    public function config()
    {
        set_option('fedora_connector_omitted_datastreams',
            $_POST['fedora_connector_omitted_datastreams']
        );
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
                    'saved-dsid' => $datastream->dsid
                ));
            }
        }

        // Add the 'Fedora' tab.
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
            $item, (int) $post['server'], $post['pid'], $post['dsid']
        );

        // Import.
        if ((bool) $post['import']) {
            $importer = new FedoraConnector_Import();
            $importer->import($datastream);
        }

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
        $tabs['Fedora Connector'] = uri('fedora-connector');
        return $tabs;
    }

    /**
     * Render the datastream in the public view of the item.
     *
     * @return void.
     */
    public function publicAppendToItemsShow()
    {
        $item = get_current_item();
        echo fedorahelpers_getItemsShow($item);
    }

}
