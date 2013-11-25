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


class FedoraConnectorPlugin extends Omeka_Plugin_AbstractPlugin
{

    protected $_hooks = array(
        'install',
        'uninstall',
        'after_save_form_item',
        'admin_theme_header',
        'define_routes',
        'admin_append_to_items_show_primary',
        'public_append_to_items_show'
    );

    protected $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main',
        //'exhibit_builder_exhibit_display_item',
        //'exhibit_builder_display_exhibit_thumbnail_gallery'
    );

    /**
     * Insert tables.
     *
     * @return void
     */
    public function hookInstall()
    {

        // Servers.
        $this->_db->query(
            "CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}fedora_connector_servers` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `url` tinytext collate utf8_unicode_ci,
                `name` tinytext collate utf8_unicode_ci,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

        // Objects.
        $this->_db->query(
            "CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}fedora_connector_objects` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `item_id` int(10) unsigned,
                `server_id` int(10) unsigned,
                `pid` tinytext collate utf8_unicode_ci,
                `dsids` tinytext collate utf8_unicode_ci,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
        );

    }

    /**
     * Drop tables.
     *
     * @return void
     */
    public function hookUninstall()
    {

        // Drop the servers table.
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}fedora_connector_servers`";
        $this->_db->query($sql);

        // Drop the objects table.
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}fedora_connector_objects`";
        $this->_db->query($sql);
    }

    /**
     * Add plugin static assets.
     *
     * @param Zend_Controller_Request_Http $request The request.
     *
     * @return void
     */
    public function hookAdminThemeHeader($request)
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
     * @param array $args Contains: `router` (Zend_Config).
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            FEDORA_CONNECTOR_PLUGIN_DIR . '/routes.ini'
        ));
    }

    /**
     * Add Fedora tab to the Items interface.
     *
     * @param array $tabs Array of tab names => markup.
     *
     * @return array Updated $tabs array.
     */
    public function filterAdminItemsFormTabs($tabs)
    {

        // Construct the form, strip the <form> tag.
        $form = new FedoraConnector_Form_Object();
        $form->removeDecorator('form');

        // Get the item.
        $item = get_current_record('item');

        // If the item is saved.
        if (!is_null($item->id)) {

            // Try to get a datastream.
            $objectsTable = $this->_db->getTable('FedoraConnectorObject');
            $object = $objectsTable->findByItem($item);

            // Populate fields.
            if ($object) {
                $form->populate(array(
                    'server' => $object->server_id,
                    'pid' => $object->pid,
                    'saved-dsids' => $object->dsids
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
    public function hookAfterSaveFormItem($item, $post)
    {

        // Create or update the datastream.
        $object = $this->_objects->createOrUpdate(
            $item, (int) $post['server'], $post['pid'], $post['dsids']
        );

        // Import.
        if ((bool) $post['import']) {
            $importer = new FedoraConnector_Import();
            $importer->import($object);
        }

    }

    /**
     * Add link to admin menu bar.
     *
     * @param array $tabs Tabs, <LABEL> => <URI> pairs.
     * @return array The tab array with the "Fedora Connector" tab.
     */
    public function filterAdminNavigationMain($tabs)
    {
        $tabs[] = array('label' => 'Fedora Connector', 'uri' => url('fedora-connector'));
        return $tabs;
    }

    /**
     * Render the datastream on admin show page.
     *
     * @return void.
     */
    public function hookAdminAppendToItemsShowPrimary()
    {
        echo fedora_connector_display_object(get_current_record('item'));
    }

    /**
     * Render the datastream on public show page.
     *
     * @return void.
     */
    public function hookPublicAppendToItemsShow()
    {
        echo fedora_connector_display_object(get_current_record('item'));
    }

    public function filterExhibitBuilderExhibitDisplayItem($html, $displayFileOptions, $linkProperties, $item)
    {
      $fedoraObject = fedora_connector_display_object($item, array('scale' => settings('fullsize_constraint')));
      $html = $fedoraObject ? exhibit_builder_link_to_exhibit_item($fedoraObject, $linkProperties, $item) : $html;
      return $html;
    }

    public function filterExhibitBuilderDisplayExhibitThumbnailGallery($html, $start, $end, $props, $thumbnailType) {

      $params = array();

      switch($thumbnailType) {
        case 'thumbnail':
          $params['scale'] = settings('thumbnail_constraint');
          break;
        case 'square_thumbnail':
          $params['region'] = '0.5,0.5,'.settings('square_thumbnail_constraint').','.settings('square_thumbnail_constraint');
          $params['level'] = 1;
          break;
      }

      $html = '';

      for ($i=(int)$start; $i <= (int)$end; $i++) {
        if (exhibit_builder_use_exhibit_page_item($i)) {
          $thumbnail = fedora_connector_display_object($item, $params) ? fedora_connector_display_object($item, $params) : item_image($thumbnailType, $props);
          $html .= "\n" . '<div class="exhibit-item">';
          $html .= exhibit_builder_link_to_exhibit_item($thumbnail);
          $html .= exhibit_builder_exhibit_display_caption($i);
          $html .= '</div>' . "\n";
        }
      }

      return $html;
    }

}
