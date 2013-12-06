<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class DCImporterTest extends FedoraConnector_Case_Default
{


    public function setUp()
    {
        parent::setup();
        $this->importer = new DC_Importer();
    }


    /**
     * `canImport` should accept the DC metadata stream.
     */
    public function testCanImport()
    {
        $this->assertTrue($this->importer->canImport('DC'));
        $this->assertFalse($this->importer->canImport('invalid'));
    }


}
