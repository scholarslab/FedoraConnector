<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Test runner class.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

require_once dirname(__FILE__) . '/../FedoraConnectorPlugin.php';
require_once dirname(__FILE__) . '/../libraries/FedoraConnector/AbstractRenderer.php';

/**
 * Set up the system for testing this plugin.
 */
class FedoraConnector_Test_AppTestCase extends Omeka_Test_AppTestCase
{

    const PLUGIN_NAME = 'FedoraConnector';

    /**
     * Set up the system for testing this plugin.
     */
    public function setUp() {

        parent::setUp();

        // Authenticate and set the current user.
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);

        // Add the plugin hooks and filters (including the install hook).
        $pluginBroker = get_plugin_broker();
        $this->_addPluginHooksAndFilters($pluginBroker, self::PLUGIN_NAME);

        $pluginHelper = new Omeka_Test_Helper_Plugin();
        $pluginHelper->setUp(self::PLUGIN_NAME);

        // Get tables.
        $this->serversTable = $this->db->getTable('FedoraConnectorServer');
        $this->objectsTable = $this->db->getTable('FedoraConnectorObject');

    }

    /**
     * Run the plugin.
     *
     * @return void.
     */
    public function _addPluginHooksAndFilters($pluginBroker, $pluginName) {
        $pluginBroker->setCurrentPluginDirName($pluginName);
        new FedoraConnectorPlugin;
    }

    /**
     * Create a server.
     *
     * @return Omeka_Record $server The server.
     */
    public function __server(
        $name='Test Server',
        $url='http://www.test.org/fedora')
    {

        $server = new FedoraConnectorServer;
        $server->name = $name;
        $server->url = $url;
        $server->save();

        return $server;

    }

    /**
     * Create an item.
     *
     * @return Omeka_Record $item The item.
     */
    public function _createItem($name)
    {

        $item = new Item;
        $item->featured = 0;
        $item->public = 1;
        $item->save();

        $element_text = new ElementText;
        $element_text->record_id = $item->id;
        $element_text->record_type_id = 2;
        $element_text->element_id = 50;
        $element_text->html = 0;
        $element_text->text = $name;
        $element_text->save();

        return $item;

    }

    /**
     * Create a collection of items.
     *
     * @return void.
     */
    public function _createItems($count)
    {
        for ($i=0; $i<$count; $i++) {
            $this->_createItem('TestingItem' . $i);
        }
    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
