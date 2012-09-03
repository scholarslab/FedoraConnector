<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DC importer unit tests.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class DCImporterTest extends FedoraConnector_Test_AppTestCase
{

    public function setUp()
    {
        parent::setup();
        $this->importer = new DC_Importer();
    }

    /**
     * canImport() should accept the DC metadata stream.
     *
     * @return void.
     */
    public function testCanImport()
    {
        $this->assertTrue($this->importer->canImport('DC'));
        $this->assertFalse($this->importer->canImport('invalid'));
    }

}
