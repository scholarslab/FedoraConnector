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


require_once dirname(__FILE__)
    . '/../../libraries/FedoraConnector/Plugins.php';


/**
 * This manages a set of plugins in a directcory.
 *
 * Each plugin is in its own file, which may have a numeric prefix for
 * ordering, and is implemented by a class with a name in the form
 * Plugin_Type.  ('Type' is passed into the PluginDir manager.)
 */
class FedoraConnector_PluginDir_Test extends PHPUnit_Framework_TestCase
{
    var $plugs;
    var $expected;

    function setUp() {
        $this->plugs = new FedoraConnector_Plugins(
            dirname(__FILE__) . '/plugs',
            'Test',
            'canProcess',
            'process'
        );
        $this->expected = array(
            'A_Test' => 1,
            'B_Test' => 2,
            'C_Test' => 3,
            'D_Test' => 4
        );
    }

    function testGetPlugins() {
        $plugList = $this->plugs->getPlugins();
        $this->assertEquals(4, count($plugList));

        $expected = array(
            'B_Test',
            'C_Test',
            'D_Test',
            'A_Test'
        );

        for ($i=0; $i<count($plugList); $i++) {
            $plug = $plugList[$i];
            $this->assertEquals($expected[$i], get_class($plug));
        }
    }

    function testFilterPlugins() {
        foreach ($this->expected as $name => $filter) {
            $actual = $this->plugs->filterPlugins($filter);
            $this->assertEquals(1, count($actual));
            $this->assertEquals($name, get_class($actual[0]));
        }

        $fail = $this->plugs->filterPlugins(10);
        $this->assertEquals(0, count($fail));
    }

    function testHasPlugin() {
        foreach ($this->expected as $name => $filter) {
            $this->assertTrue($this->plugs->hasPlugin($filter));
        }

        $this->assertFalse($this->plugs->hasPlugin(10));
    }

    function testGetPlugin() {
        foreach ($this->expected as $name => $filter) {
            $actual = $this->plugs->getPlugin($filter);
            $this->assertEquals($name, get_class($actual));
        }

        $this->assertNull($this->plugs->getPlugin(10));
    }

    function testCallFirst() {
        foreach ($this->expected as $name => $filter) {
            $this->assertEquals(
                "{$name}_{$filter}",
                $this->plugs->callFirst($filter)
            );
        }

        $this->assertNull($this->plugs->callFirst(10));
    }

    function testCallAll() {
        foreach ($this->expected as $name => $filter) {
            $actual = $this->plugs->callAll($filter);
            $this->assertEquals(1, count($actual));
            $this->assertEquals("{$name}_{$filter}", $actual[0]);
        }

        $this->assertEquals(0, count($this->plugs->callAll(10)));
    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
