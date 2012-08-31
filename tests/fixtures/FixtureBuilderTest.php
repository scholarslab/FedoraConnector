<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Fixture generators.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_FixtureBuilderTest extends FedoraConnector_Test_AppTestCase
{

    private $_fixtures = null;

    /**
     * Instantiate the helper class, install the plugins, get the database.
     *
     * @return void.
     */
    public function setUp()
    {
        parent::setUp();
        $this->_fixtures = FEDORA_CONNECTOR_PLUGIN_DIR . '/spec/javascripts/fixtures/';
    }

    /**
     * Build item add markup.
     *
     * @return void.
     */
    public function testBuildItemAddMarkup()
    {

        // Generate and write the fixture.
        $fixture = fopen($this->_fixtures . 'item-add.html', 'w');
        $this->dispatch('items/add');
        $response = $this->getResponse()->getBody('default');
        fwrite($fixture, $response);
        fclose($fixture);

    }

    /**
     * Build item edit markup.
     *
     * @return void.
     */
    public function testBuildItemEditMarkup()
    {

        // Create an item.
        $item = $this->__item();
        $server = $this->__server();

        // Create an existing object.
        $object = $this->__object($item, $server);

        // Generate and write the fixture.
        $fixture = fopen($this->_fixtures . 'item-edit.html', 'w');
        $this->dispatch('items/edit/' . $item->id);
        $response = $this->getResponse()->getBody('default');
        fwrite($fixture, $response);
        fclose($fixture);

    }

    /**
     * Build datastreams json response.
     *
     * @return void.
     */
    public function testBuildDatastreamsJson()
    {

        // Mock Fedora response.
        $this->__mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->__server();

        // Mock POST.
        $this->request->setMethod('GET')
            ->setParams(array(
                'server' => $server->id,
                'pid' => 'pid:test'
            )
        );

        // Hit /query-datastreams.
        $this->dispatch('fedora-connector/datastreams/query-datastreams');
        $response = $this->getResponse()->getBody('default');

        $fixture = fopen($this->_fixtures . 'datastreams-json.html', 'w');
        fwrite($fixture, $response);
        fclose($fixture);

    }

}
