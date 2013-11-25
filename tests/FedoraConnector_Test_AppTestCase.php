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
require_once dirname(__FILE__) . '/../libraries/FedoraConnector/AbstractImporter.php';
require_once dirname(__FILE__) . '/../Importers/DC.php';
require_once dirname(__FILE__) . '/../Renderers/000-Jp2.php';


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
        $this->itemsTable = $this->db->getTable('Item');

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
     * Test helpers.
     */


    /**
     * Create an item.
     *
     * @return Omeka_record $item The item.
     */
    public function __item()
    {
        $item = new Item;
        $item->save();
        return $item;
    }

    /**
     * Create a server.
     *
     * @param string $name The server name.
     * @param string $url The server url.
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
     * Create a service.
     *
     * @param OmekaItem $item Parent item.
     * @param OmekaItem $server Parent server.
     * @param string $pid The object pid.
     * @param string $dsids Comma-deliimited dsids.
     *
     * @return Omeka_Record $object The object.
     */
    public function __object(
        $item=null,
        $server=null,
        $pid='pid:test',
        $dsids='DC,content'
    )
    {

        // If no item, create one.
        if (is_null($item)) {
            $item = $this->__item();
        }

        // If no server, create one.
        if (is_null($server)) {
            $server = $this->__server();
        }

        $object = new FedoraConnectorObject();
        $object->item_id = $item->id;
        $object->server_id = $server->id;
        $object->pid = $pid;
        $object->dsids = $dsids;
        $object->save();

        return $object;

    }

    /**
     * Set a mock FedoraGateway class.
     *
     * @param string $fixture The name of the fixture xml.
     * @param string $query The xpath query to run on the fixture.
     *
     * @return void.
     */
    public function __mockFedora($fixture, $query)
    {

        // Generate response.
        $gateway = new FedoraGateway();
        $url = FEDORA_PLUGIN_DIR . '/tests/xml/' . $fixture;
        $response = $gateway->query($url, $query);

        // Mock the gateway.
        $mock = $this->getMock('FedoraGateway');
        $mock->expects($this->any())->method('query')->will($this->returnValue($response));
        Zend_Registry::set('gateway', $mock);

    }

    /**
     * Prepare mock FedoraGateway class for import integration tests.
     *
     * @param string $versionFixture The name of the version fixture xml.
     * @param string $metadataFixture The name of the metadata xml.
     *
     * @return void.
     */
    public function __mockImport($versionFixture, $metadataFixture)
    {

        // Generate response for getVersion() call.
        $gateway = new FedoraGateway();
        $url = FEDORA_PLUGIN_DIR . '/tests/xml/' . $versionFixture;
        $getVersionResponse = $gateway->query($url, "//*[local-name() = 'repositoryVersion']");

        // Generate response for getMetadataXml() call.
        $url = FEDORA_PLUGIN_DIR . '/tests/xml/' . $metadataFixture;
        $getMetadataXmlResponse = $gateway->load($url);

        // Mock the gateway.
        $mock = $this->getMock('FedoraGateway');
        $mock->expects($this->any())->method('query')->will($this->returnValue($getVersionResponse));
        $mock->expects($this->any())->method('load')->will($this->returnValue($getMetadataXmlResponse));
        Zend_Registry::set('gateway', $mock);

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
