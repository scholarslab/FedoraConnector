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
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */
?>

<?php

class FedoraConnector_ServersControllerTest extends Omeka_Test_AppTestCase
{

    public function setUp()
    {

        parent::setUp();
        $this->helper = new FedoraConnector_Test_AppTestCase;
        $this->helper->setUp();
        $this->db = get_db();

    }

    public function testDefaultRedirect()
    {

        $this->dispatch('fedora-connector');
        $this->assertController('servers');
        $this->assertAction('browse');

    }

    public function testDefaultServer()
    {

        $this->dispatch('fedora-connector');
        $this->assertEquals(1, $this->db->getTable('FedoraConnectorServer')->count());
        $this->assertQueryContentContains('strong', 'Default Fedora Server');
        $this->assertQueryContentContains('a', 'http://localhost:8080/fedora/');

    }

    public function testAddServer()
    {

        $this->assertEquals(1, $this->db->getTable('FedoraConnectorServer')->count());

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServer',
                'url' => 'http://TestUrl',
                'is_default' => 0
            )
        );

        $this->dispatch('fedora-connector/servers/insert');
        $this->assertEquals(1, $this->db->getTable('FedoraConnectorServer')->count());

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServer',
                'url' => 'http://TestUrl.com/fedora/',
                'is_default' => 0
            )
        );

        $this->dispatch('fedora-connector/servers/insert');
        $this->assertEquals(2, $this->db->getTable('FedoraConnectorServer')->count());

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServer2',
                'url' => 'http://TestUrl.com/fedora/',
                'is_default' => 1
            )
        );

        $this->dispatch('fedora-connector/servers/insert');
        $this->assertEquals(3, $this->db->getTable('FedoraConnectorServer')->count());
        $this->assertEquals(1, count($this->db->getTable('FedoraConnectorServer')->findBySql('is_default = ?', array(1))));

    }

    public function testEditServer()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServer',
                'url' => 'http://TestUrl.com/fedora/',
                'is_default' => 0
            )
        );

        $this->dispatch('fedora-connector/servers/insert');
        $this->assertEquals(2, $this->db->getTable('FedoraConnectorServer')->count());

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServer',
                'url' => 'http://TestUrl.com/fedora/',
                'is_default' => 1,
                'id' => 2,
                'edit_submit' => 'Save'
            )
        );

        $this->dispatch('fedora-connector/servers/edit/update');
        $this->assertEquals(1, count($this->db->getTable('FedoraConnectorServer')->findBySql('is_default = ?', array(1))));

        $this->request->setMethod('POST')
            ->setPost(array(
                'name' => 'TestServerUpdate',
                'url' => 'http://TestUrl.com/fedora/',
                'is_default' => 1,
                'id' => 2,
                'edit_submit' => 'Save'
            )
        );

        $this->dispatch('fedora-connector/servers/edit/update');
        $this->assertEquals('TestServerUpdate', $this->db->getTable('FedoraConnectorServer')->find(2)->name);

    }

    public function testDeleteServer()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'deleteconfirm_submit' => 'Delete'
            )
        );

        $this->dispatch('fedora-connector/servers/delete/1');
        $this->assertEquals(0, $this->db->getTable('FedoraConnectorServer')->count());

    }

}
