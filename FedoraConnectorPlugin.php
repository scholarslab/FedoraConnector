<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorPlugin extends Omeka_Plugin_AbstractPlugin
{


    protected $_hooks = array(
        'install',
        'uninstall',
        'define_routes',
        'after_save_item',
        'admin_head',
        'admin_items_show',
        'public_items_show'
    );


    protected $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main',
        //'exhibit_builder_exhibit_display_item',
        //'exhibit_builder_display_exhibit_thumbnail_gallery'
    );


    /**
     * Load the objects table.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objects = $this->_db->getTable('FedoraConnectorObject');
    }


    /**
     * Create servers and objects tables.
     */
    public function hookInstall()
    {

        $this->_db->query(<<<SQL
        CREATE TABLE IF NOT EXISTS
            {$this->_db->prefix}fedora_connector_servers (

            id          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name        TINYTEXT NOT NULL,
            url         TINYTEXT NOT NULL,

            PRIMARY KEY (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
);

        $this->_db->query(<<<SQL
        CREATE TABLE IF NOT EXISTS
            {$this->_db->prefix}fedora_connector_objects (

            id          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            item_id     INT(10) UNSIGNED NOT NULL,
            server_id   INT(10) UNSIGNED NOT NULL,
            pid         TINYTEXT NOT NULL,
            dsids       TINYTEXT NOT NULL,

            PRIMARY KEY (id)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
);

    }


    /**
     * Drop tables.
     */
    public function hookUninstall()
    {
        $this->_db->query(<<<SQL
        DROP TABLE {$this->_db->prefix}fedora_connector_servers
SQL
);
        $this->_db->query(<<<SQL
        DROP TABLE {$this->_db->prefix}fedora_connector_objects
SQL
);
    }


    /**
     * Register routes.
     *
     * @param array $args Contains: `router` (Zend_Config).
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            FEDORA_DIR . '/routes.ini'
        ));
    }


    /**
     * Add plugin static assets.
     *
     * @param Zend_Controller_Request_Http $request The request.
     */
    public function hookAdminHead($args)
    {

        // Get request module and action.
        $controller = Zend_Controller_Front::getInstance();
        $module = $controller->getRequest()->getModuleName();
        $action = $controller->getRequest()->getActionName();

        // Server browse CSS:
        if ($module == 'fedora-connector' && $action == 'browse') {
            queue_css_file('browse');
        }

        // Item edit JS:
        if ($module == 'default' && $action == 'edit') {
            queue_js_file('vendor/load/load');
            queue_js_file('load-datastreams');
        }

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

        // Get the form markup.
        $form = new FedoraConnector_Form_Object();
        $form->removeDecorator('form');

        // Get the item.
        $item = get_current_record('item');

        if ($item->exists()) {

            // Try to get a datastream.
            $object = $this->_objects->findByItem($item);

            // Populate fields.
            if ($object) {
                $form->populate(array(
                    'server'        => $object->server_id,
                    'pid'           => $object->pid,
                    'saved-dsids'   => $object->dsids
                ));
            }
        }

        // Add tab.
        $tabs['Fedora'] = $form;
        return $tabs;

    }


    /**
     * Save / update datastream, import from Fedora.
     *
     * @param array $args
     */
    public function hookAfterSaveItem($args)
    {

        $item = $args['record'];
        $post = $args['post'];

        // TODO|refactor
        // Only try to run the import if the Item form is being saved, and a
        // POST array is defined. Is there no clearer way to do this?
        if (!$post) return;

        // Create or update the datastream.
        $object = $this->_objects->createOrUpdate(
            $item, (int) $post['server'], $post['pid'], $post['dsids']
        );

        // Perform the import.
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
        $tabs[] = array(
            'label' => 'Fedora', 'uri' => url('fedora-connector')
        );
        return $tabs;
    }


    /**
     * Render the datastream on admin show page.
     */
    public function hookAdminItemsShow()
    {
        echo fc_displayObject(get_current_record('item'));
    }


    /**
     * Render the datastream on public show page.
     */
    public function hookPublicItemsShow()
    {
        echo fc_displayObject(get_current_record('item'));
    }


    // TODO: Migrate to ExhibitBuilder 2.x, shorten lines to 80 chars.


    //public function filterExhibitBuilderExhibitDisplayItem(
        //$html, $displayFileOptions, $linkProperties, $item) {

        //$object = fc_displayObject(
            //$item, array('scale' => settings('fullsize_constraint'))
        //);

        //$html = $fedoraObject ? exhibit_builder_link_to_exhibit_item($fedoraObject, $linkProperties, $item) : $html;
        //return $html;

    //}


    //public function filterExhibitBuilderDisplayExhibitThumbnailGallery($html, $start, $end, $props, $thumbnailType) {

      //$params = array();

      //switch($thumbnailType) {
        //case 'thumbnail':
          //$params['scale'] = settings('thumbnail_constraint');
          //break;
        //case 'square_thumbnail':
          //$params['region'] = '0.5,0.5,'.settings('square_thumbnail_constraint').','.settings('square_thumbnail_constraint');
          //$params['level'] = 1;
          //break;
      //}

      //$html = '';

      //for ($i=(int)$start; $i <= (int)$end; $i++) {
        //if (exhibit_builder_use_exhibit_page_item($i)) {
          //$thumbnail = fc_displayObject($item, $params) ? fc_displayObject($item, $params) : item_image($thumbnailType, $props);
          //$html .= "\n" . '<div class="exhibit-item">';
          //$html .= exhibit_builder_link_to_exhibit_item($thumbnail);
          //$html .= exhibit_builder_exhibit_display_caption($i);
          //$html .= '</div>' . "\n";
        //}
      //}

      //return $html;
    //}


}
