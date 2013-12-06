<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_FixtureBuilderTest
    extends FedoraConnector_Case_Default
{


    private $_fixtures = null;


    /**
     * Instantiate the helper class, install the plugins, get the database.
     */
    public function setUp()
    {
        parent::setUp();
        $this->_fixtures = FEDORA_DIR . '/spec/javascripts/fixtures/';
    }


    /**
     * Build item add markup.
     */
    public function testBuildItemAddMarkup()
    {

        // Build form.
        $form = new FedoraConnector_Form_Object();

        // Generate and write the fixture.
        $fixture = fopen($this->_fixtures . 'item-add.html', 'w');
        fwrite($fixture, $form);
        fclose($fixture);

    }


    /**
     * Build item edit markup.
     */
    public function testBuildItemEditMarkup()
    {
        // Create an item.
        $item = $this->_item();
        $server = $this->_server();

        // Create an existing object.
        $object = $this->_object($item, $server);

        // Build form.
        $form = new FedoraConnector_Form_Object();
        $form->populate(array(
            'server' => $object->server_id,
            'pid' => $object->pid,
            'saved-dsids' => $object->dsids
        ));

        // Generate and write the fixture.
        $fixture = fopen($this->_fixtures . 'item-edit.html', 'w');
        fwrite($fixture, $form);
        fclose($fixture);

    }


    /**
     * Build datastreams json response.
     */
    public function testBuildDatastreamsJson()
    {

        // Mock Fedora response.
        $this->_mockFedora(
            'datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->_server();

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
