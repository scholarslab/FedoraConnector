<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Items controller integration tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnector_ItemsControllerTest extends FedoraConnector_Test_AppTestCase
{

    /**
     * The 'Fedora' tab should display the datastreams form.
     *
     * @return void.
     */
    public function testItemAddTab()
    {

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Hit item add.
        $this->dispatch('items/add');

        // Tab:
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#fedora-metadata"]', 'Fedora'
        );

        // Server:
        $this->assertXpathContentContains(
            '//select[@name="server"]/option[@value="'.$server1->id.'"]', 'Test Title 1'
        );
        $this->assertXpathContentContains(
            '//select[@name="server"]/option[@value="'.$server2->id.'"]', 'Test Title 2'
        );

        // PID:
        $this->assertXpath('//input[@id="pid"][@name="pid"]');

        // Datastreams:
        $this->assertXpath('//select[@id="dsids"][@name="dsids[]"]');

        // Import:
        $this->assertXpath('//input[@id="import"][@name="import"]');

    }

    /**
     * The 'Fedora' tab should display the datastreams form.
     *
     * @return void.
     */
    public function testItemEditTab()
    {

        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Create item.
        $item = $this->__item();

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Tab:
        $this->assertXpathContentContains(
            '//ul[@id="section-nav"]/li/a[@href="#fedora-metadata"]', 'Fedora'
        );

        // Server:
        $this->assertXpathContentContains(
            '//select[@name="server"]/option[@value="'.$server1->id.'"]', 'Test Title 1'
        );
        $this->assertXpathContentContains(
            '//select[@name="server"]/option[@value="'.$server2->id.'"]', 'Test Title 2'
        );

        // PID:
        $this->assertXpath('//input[@id="pid"][@name="pid"]');

        // Datastreams:
        $this->assertXpath('//select[@id="dsids"][@name="dsids[]"]');

        // Import:
        $this->assertXpath('//input[@id="import"][@name="import"]');

    }

    /**
     * If there is an existing Fedora object for the item, the data should be
     * populated in the textareas.
     *
     * @return void.
     */
    public function testItemEditData()
    {

        // Create item.
        $item = $this->__item();

        // Create servers.
        $server1 = $this->__server('Test Title 1', 'http://test1.org/fedora');
        $server2 = $this->__server('Test Title 2', 'http://test2.org/fedora');

        // Create Fedora object.
        $object = $this->__object($item, $server2);

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Server:
        $this->assertXpath('//select[@name="server"]/option[@value="'.$server2->id.'"]
            [@selected="selected"]'
        );

        // PID:
        $this->assertXpath('//input[@id="pid"][@name="pid"][@value="pid:test"]');

        // Hidden fields:
        $this->assertXpath('//input[@type="hidden"][@name="saveddsids"][@value="DC,content"]');
        $this->assertXpath('//input[@type="hidden"][@name="datastreamsuri"]');

    }

    /**
     * When an item is added and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectCreationOnItemAdd()
    {

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'tags' => '',
                'server' => 1,
                'pid' => 'pid:test',
                'dsids' => array('DC', 'content'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/add');

        // +1 editions.
        $this->assertEquals($this->objectsTable->count(), $count+1);

        // Get out service and check.
        $object = $this->__getLastRow($this->objectsTable);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }

    /**
     * When an item is added and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    public function testImportOnItemAdd()
    {

        // Create server.
        $server = $this->__server();

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'tags' => '',
                'server' => $server->id,
                'pid' => 'pid:test',
                'dsids' => array('DC'),
                'import' => 1
            )
        );

        // Mock Fedora.
        $this->__mockImport('describe-v3x.xml', 'dc.xml');

        // Hit item edit.
        $this->dispatch('items/add');

        // Get the new item.
        $item = $this->__getLastRow($this->itemsTable);

        // Title.
        $title = metadata($item, array('Dublin Core', 'Title'));
        $this->assertEquals($title, 'Dr. J.S. Grasty');

        // Contributor
        $contributor = metadata($item, array('Dublin Core', 'Contributor'));
        $this->assertEquals($contributor, 'Holsinger, Rufus W., 1866-1930');

        // Types.
        $types = metadata($item, array('Dublin Core', 'Type'), array('all' => true));
        $this->assertEquals($types[0], 'Collection');
        $this->assertEquals($types[1], 'StillImage');
        $this->assertEquals($types[2], 'Photographs');

        // Formats.
        $formats = metadata($item, array('Dublin Core', 'Format'), array('all' => true));
        $this->assertEquals($formats[0], 'Glass negatives');
        $this->assertEquals($formats[1], 'image/jpeg');

        // Description.
        $description = metadata($item, array('Dublin Core', 'Description'));
        $this->assertEquals($description, 'With Child, Two Poses');

        // Subjects.
        $subjects = metadata($item, array('Dublin Core', 'Subject'), array('all' => true));
        $this->assertEquals($subjects[0], 'Photography');
        $this->assertEquals($subjects[1], 'Portraits, Group');
        $this->assertEquals($subjects[2], 'Children');
        $this->assertEquals($subjects[3], 'Holsinger Studio (Charlottesville, Va.)');

        // Identifiers.
        $identifiers = metadata($item, array('Dublin Core', 'Identifier'), array('all' => true));
        $this->assertEquals($identifiers[0], 'H03424B');
        $this->assertEquals($identifiers[1], 'uva-lib:1038848');
        $this->assertEquals($identifiers[2], '39667');
        $this->assertEquals($identifiers[3], 'uri: uva-lib:1038848');
        $this->assertEquals($identifiers[4], '7688');
        $this->assertEquals($identifiers[5], '365106');
        $this->assertEquals($identifiers[6], '000007688_0004.tif');
        $this->assertEquals($identifiers[7], 'MSS 9862');

    }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectCreationOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'tags' => '',
                'server' => 1,
                'pid' => 'pid:test',
                'dsids' => array('DC', 'content'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // +1 objects.
        $this->assertEquals($this->objectsTable->count(), $count+1);

        // Get out service and check.
        $object = $this->__getLastRow($this->objectsTable);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test');
        $this->assertEquals($object->dsids, 'DC,content');

    }

    /**
     * When an item is edited and Fedora data is entered, the service should
     * be created.
     *
     * @return void.
     */
    public function testFedoraObjectUpdateOnItemEdit()
    {

        // Create item.
        $item = $this->__item();

        // Create Fedora object.
        $object = $this->__object($item);

        // Capture starting count.
        $count = $this->objectsTable->count();

        // Set exhibit id.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'tags' => '',
                'server' => 1,
                'pid' => 'pid:test2',
                'dsids' => array('DC2', 'content2'),
                'import' => 0
            )
        );

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // +0 editions.
        $this->assertEquals($this->objectsTable->count(), $count);

        // Get out service and check.
        $object = $this->__getLastRow($this->objectsTable);
        $this->assertEquals($object->server_id, 1);
        $this->assertEquals($object->pid, 'pid:test2');
        $this->assertEquals($object->dsids, 'DC2,content2');

    }

    /**
     * When an item is edited and the "Import now?" checkbox is checked,
     * the datastreams should be imported.
     *
     * @return void.
     */
    public function testImportOnItemEdit()
    {

        // Create item and object.
        $item = $this->__item();
        $this->__object($item);

        // Mock post.
        $this->request->setMethod('POST')
            ->setPost(array(
                'public' => 1,
                'featured' => 0,
                'Elements' => array(),
                'order' => array(),
                'tags' => '',
                'server' => 1,
                'pid' => 'pid:test',
                'dsids' => array('DC'),
                'import' => 1
            )
        );

        // Mock Fedora.
        $this->__mockImport('describe-v3x.xml', 'dc.xml');

        // Hit item edit.
        $this->dispatch('items/edit/' . $item->id);

        // Title.
        $title = $item->getElementTextsByElementNameAndSetName('Title', 'Dublin Core');
        $this->assertEquals($title[0]->text, 'Dr. J.S. Grasty');

        // Contributor
        $contributor = $item->getElementTextsByElementNameAndSetName('Contributor', 'Dublin Core');
        $this->assertEquals($contributor[0]->text, 'Holsinger, Rufus W., 1866-1930');

        // Types.
        $types = $item->getElementTextsByElementNameAndSetName('Type', 'Dublin Core');
        $this->assertEquals($types[0]->text, 'Collection');
        $this->assertEquals($types[1]->text, 'StillImage');
        $this->assertEquals($types[2]->text, 'Photographs');

        // Formats.
        $formats = $item->getElementTextsByElementNameAndSetName('Format', 'Dublin Core');
        $this->assertEquals($formats[0]->text, 'Glass negatives');
        $this->assertEquals($formats[1]->text, 'image/jpeg');

        // Description.
        $description = $item->getElementTextsByElementNameAndSetName('Description', 'Dublin Core');
        $this->assertEquals($description[0]->text, 'With Child, Two Poses');

        // Subjects.
        $subjects = $item->getElementTextsByElementNameAndSetName('Subject', 'Dublin Core');
        $this->assertEquals($subjects[0]->text, 'Photography');
        $this->assertEquals($subjects[1]->text, 'Portraits, Group');
        $this->assertEquals($subjects[2]->text, 'Children');
        $this->assertEquals($subjects[3]->text, 'Holsinger Studio (Charlottesville, Va.)');

        // Identifiers.
        $identifiers = $item->getElementTextsByElementNameAndSetName('Identifier', 'Dublin Core');
        $this->assertEquals($identifiers[0]->text, 'H03424B');
        $this->assertEquals($identifiers[1]->text, 'uva-lib:1038848');
        $this->assertEquals($identifiers[2]->text, '39667');
        $this->assertEquals($identifiers[3]->text, 'uri: uva-lib:1038848');
        $this->assertEquals($identifiers[4]->text, '7688');
        $this->assertEquals($identifiers[5]->text, '365106');
        $this->assertEquals($identifiers[6]->text, '000007688_0004.tif');
        $this->assertEquals($identifiers[7]->text, 'MSS 9862');

    }

    /**
     * When an item has a Fedora object with a dsid activated that has
     * a renderer, the dsid should be rendered at the bottom of the admin
     * item show page.
     *
     * @return void.
     */
    public function testRenderOnItemAdminShow()
    {

        // Create item and object.
        $item = $this->__item();
        $this->__object($item);

        // Mock getMimeType().
        $this->__mockFedora('datastreams.xml',
            "//*[local-name() = 'datastream'][@dsid='content']"
        );

        // Hit item show.
        $this->dispatch('items/show/' . $item->id);

        // Check for image.
        $this->assertXpath('//img[@class="fedora-renderer"]');

    }

}
