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
        $this->helper->setUpPlugin();
        $this->db = get_db();

    }

    public function testDefaultServer()
    {

        $this->dispatch('bag-it');
        $this->assertQueryContentContains('p', 'There are no collections. Create one!');
        $this->assertEquals(0, $this->db->getTable('BagitFileCollection')->count());

    }

    public function testAddCollection()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'collection_name' => 'Testing Collection'
            )
        );

        $this->dispatch('bag-it/collections/addcollection');
        $this->assertQueryContentContains('a', 'Testing Collection');
        $this->assertEquals(1, $this->db->getTable('BagitFileCollection')->count());

    }

    public function testRejectBlankCollectionName()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'collection_name' => ''
            )
        );

        $this->dispatch('bag-it/collections/addcollection');
        $this->assertQueryContentContains('div.error', 'Enter a name for the collection');

    }

    public function testCollectionNameTrim()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'collection_name' => '    '
            )
        );

        $this->dispatch('bag-it/collections/addcollection');
        $this->assertQueryContentContains('div.error', 'Enter a name for the collection');

    }

    public function testDeleteCollection()
    {

        $this->request->setMethod('POST')
            ->setPost(array(
                'collection_name' => 'Testing Collection'
            )
        );

        $this->dispatch('bag-it/collections/addcollection');
        $this->assertQueryContentContains('a', 'Testing Collection');
        $this->assertEquals(1, $this->db->getTable('BagitFileCollection')->count());

        $this->request->setMethod('POST')
            ->setPost(array(
                'confirm' => 'true'
            )
        );

        $this->dispatch('bag-it/collections/1/delete');
        $this->assertEquals(0, $this->db->getTable('BagitFileCollection')->count());

    }

    public function testDetectNoFilesToAdd()
    {

        $this->helper->_createFileCollection('Test Collection');
        $this->dispatch('bag-it/collections/1/add');
        $this->assertQueryContentContains('p', 'There are no files on the site that can be added to a Bag.');

    }

    public function testAddAndRemoveFiles()
    {

        $this->helper->_createItem('Testing Item');
        $this->helper->_createFiles();
        $this->helper->_createFileCollection('Test Collection');

        $this->request->setMethod('POST')
            ->setPost(array(
                'file' => array(
                    '3' => 'add',
                    '4' => 'add',
                    '5' => 'add'
                )
            )
        );

        $this->dispatch('bag-it/collections/1/add');
        $this->assertQueryCount(3, 'input[value="remove"]');

        $this->dispatch('bag-it/collections/1');
        $this->assertQueryContentContains('h2', '"Test Collection" contains 3 files:');

        $this->request->setMethod('POST')
            ->setPost(array(
                'file' => array(
                    '3' => 'remove',
                    '4' => 'remove'
                )
            )
        );

        $this->dispatch('bag-it/collections/1');
        $this->assertQueryContentContains('h2', '"Test Collection" contains 1 files:');

        $this->resetRequest()->resetResponse();

        $this->dispatch('bag-it/collections/1/add');
        $this->assertQueryCount(1, 'input[value="remove"]');

        $this->request->setMethod('POST')
            ->setPost(array(
                'file' => array(
                    '5' => 'remove'
                )
            )
        );

        $this->dispatch('bag-it/collections/1/add');
        $this->assertQueryCount(0, 'input[value="remove"]');

    }

    public function testAddAndRemoveAllFiles()
    {

        $this->helper->_createItem('Testing Item');
        $this->helper->_createFiles();
        $this->helper->_createFileCollection('Test Collection');

        $this->request->setMethod('POST')
            ->setPost(array(
                'add_all_files' => 'Add All Files'
                )
            );

        $this->dispatch('bag-it/collections/1/add');
        $this->assertEquals(13, count($this->db->getTable('BagitFileCollectionAssociation')
            ->findBySql('collection_id = ?', array(1))));

        $this->resetRequest()->resetResponse();

        $this->request->setMethod('POST')
            ->setPost(array(
                'remove_all_files' => 'Remove All Files'
                )
            );

        $this->dispatch('bag-it/collections/1/add');
        $this->assertEquals(0, count($this->db->getTable('BagitFileCollectionAssociation')
            ->findBySql('collection_id = ?', array(1))));

    }

}
