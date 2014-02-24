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


    /**
     * Item add markup.
     */
    public function testBuildItemAddMarkup()
    {
        $form = new FedoraConnector_Form_Object();
        $this->_writeFixture($form, 'item-add.html');
    }


    /**
     * Build item edit markup.
     */
    public function testBuildItemEditMarkup()
    {

        $server = $this->_server();
        $item   = $this->_item();
        $object = $this->_object($item, $server);

        // Build the form.
        $form = new FedoraConnector_Form_Object();
        $form->populate(array(
            'server'        => $object->server_id,
            'saved-dsids'   => $object->dsids,
            'pid'           => $object->pid
        ));

        // Write the fixture.
        $this->_writeFixture($form, 'item-edit.html');

    }


    /**
     * Datastreams JSON.
     */
    public function testBuildDatastreamsJson()
    {

        // Mock Fedora response.
        $this->_mockFedora('datastreams.xml',
            "//*[local-name() = 'datastream']"
        );

        // Create server.
        $server = $this->_server();

        // Set request parameters.
        $this->request->setMethod('GET')->setParams(array(
            'server' => $server->id, 'pid' => 'pid:test'
        ));

        // Write the fixture.
        $this->_writeFixtureFromRoute(
            'fedora-connector/datastreams', 'datastreams.json'
        );

    }


}
