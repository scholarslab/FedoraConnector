<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Unit tests for helper functions.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package omeka
 * @subpackage BagIt
 * @author Scholars' Lab
 * @author David McClure (david.mcclure@virginia.edu)
 * @copyright 2011
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 *
 * PHP version 5
 *
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

}
