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

class FedoraConnector_HelpersTest extends Omeka_Test_AppTestCase
{

    public function setUp()
    {

        parent::setUp();
        $this->helper = new FedoraConnector_Test_AppTestCase;
        $this->helper->setUp();
        $this->db = get_db();

    }

    public function testFedoraHelpersColumnSorting()
    {

        $this->assertEquals(fedorahelpers_doColumnSortProcessing('name', ''), 'name DESC');
        $this->assertEquals(fedorahelpers_doColumnSortProcessing('name', 'd'), 'name DESC');
        $this->assertEquals(fedorahelpers_doColumnSortProcessing('name', 'a'), 'name ASC');

    }

    public function testFedoraHelpersGetQueryNodes()
    {

        $url = FEDORA_CONNECTOR_PLUGIN_DIR . '/tests/_files/testXML.xml';

        $query = '//*[local-name() = "datastream"]';
        $nodes = fedorahelpers_getQueryNodes($url, $query);
        $this->assertEquals(8, $nodes->length);

        $query = '//*[local-name() = "datastream"][@dsid="content"]';
        $nodes = fedorahelpers_getQueryNodes($url, $query);
        $this->assertEquals(1, $nodes->length);
        $this->assertEquals('image/jp2', $nodes->item(0)->getAttribute('mimeType'));

    }

    public function testFedoraHelpersGetItems()
    {

        $this->helper->_createItems(20);
        $items = fedorahelpers_getItems();
        $this->assertEquals(20, count($items));
        $items = fedorahelpers_getItems(1, null, null);
        $this->assertEquals(get_option('per_page_admin'), count($items));
        $items = fedorahelpers_getItems(1, null, '12');
        $this->assertEquals(1, count($items));
        $this->assertEquals($items[0]->item_name, 'TestingItem12');

    }

    public function testFedoraHelpersGetSingleItem()
    {

        $this->helper->_createItems(1);
        $items = fedorahelpers_getItems();
        $this->assertEquals(1, count($items));

    }

}
