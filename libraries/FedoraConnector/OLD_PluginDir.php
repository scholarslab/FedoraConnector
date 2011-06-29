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


class FedoraConnector_PluginDir
{

    //{{{ properties

    /**
     * This is the directory containing the plugins.
     *
     * @var string
     */
    var $dirname;

    /**
     * This is the type suffix for the plugins. Plugin classes must have 
     * "_{$tag}" suffixes.
     *
     * @var string
     */
    var $tag;

    /**
     * This is the name of the method on the plugin class to use when filtering 
     * the plugins.
     *
     * @var string
     */
    var $filter;

    /**
     * This is the name of the method on the plugin class to use when 
     * performing the plugin action.
     *
     * @var string
     */
    var $action;

    /**
     * This is the list of plugin objects.
     *
     * @var array
     */
    var $plugins;

    //}}}

    /**
     * This constructs a PluginDir object.
     *
     * @param string $dirname The name of the directory to look for plugin 
     * files in.
     * @param string $tag     The suffix to use for identifying plugin classes.
     * @param string $filter  The name of the plugin class method to use when
     * filtering the plugins.
     * @param string $action  The name of the plugin class method to use when
     * performing the plugin's action.
     */
    function __construct($dirname, $tag, $filter, $action) {
        $this->dirname = $dirname;
        $this->tag = $tag;
        $this->filter = $filter;
        $this->action = $action;

        $this->plugins = $this->_discover();
    }

    /**
     * This walks $dirname to find the classes of plugins.
     *
     * @return array The list of plugin objects.
     */
    private function _discover() {
        $dir = new DirectoryIterator($this->dirname);

        $files = array();
        foreach ($dir as $file) {
            if ($file->isFile() && !$file->isDot()) {
                $filename = $file->getFilename();
                $pathname = $file->getPathname();

                $matches = array();
                if (
                    preg_match('/^(\d+\W*)?(\w+)\.php$/', $filename, $matches)
                ) {
                    require_once($pathname);
                    $class = "{$matches[2]}_{$this->tag}";
                    $files[$filename] = new $class();
                }
            }
        }

        ksort($files);
        $plugins = array_values($files);
        return $plugins;
    }

    /**
     * This returns the plugins.
     *
     * @return array The list of plugins.
     */
    function getPlugins() {
        return $this->plugins;
    }

    /**
     * This returns all plugins that pass the filter predicate.
     *
     * @param mixed  $arg       An argument to pass to the predicate method.
     *
     * @return array A list of the plugin objects that pass the predicate.
     */
    function filterPlugins($arg) {
        $predicate = $this->filter;
        $results = array();

        foreach ($this->plugins as $plug) {
            if ($plug->$predicate($arg)) {
                array_push($results, $plug);
            }
        }

        return $results;
    }

    /**
     * This tests for any plugin that passes the predicate.
     *
     * @param mixed  $arg       An argument to pass to the predicate method.
     *
     * @return bool True if any plugin object passes the predicate call.
     */
    function hasPlugin($arg) {
        return ($this->getPlugin($arg) !== null);
    }

    /**
     * This returns the first plugin that passes the predicate.
     *
     * @param mixed  $arg       An argument to pass to the predicate method.
     *
     * @return mixed This returns the first plugin object that passes the 
     * predicate.
     */
    function getPlugin($arg) {
        $predicate = $this->filter;

        foreach ($this->plugins as $plug) {
            if ($plug->$predicate($arg)) {
                return $plug;
            }
        }
        return null;
    }

    /**
     * This calls an action on the first plugin object that passes the 
     * predicate.
     *
     * @param mixed  $arg       An argument to pass to the predicate and action
     * methods.
     *
     * @return mixed This returns the result of calling the action on the first 
     * plugin object that passes the predicate.
     */
    function callFirst($arg) {
        $plug = $this->getPlugin($arg);

        if ($plug === null) {
            return null;
        } else {
            $action = $this->action;
            return $plug->$action($arg);
        }
    }

    /**
     * This calls an action on all plugin objects that pass the predicate.
     *
     * @param mixed  $arg       An argument to pass to the predicate and action
     * methods.
     *
     * @return array This returns the result of calling the action on the
     * plugin objects that pass the predicate.
     */
    function callAll($arg) {
        $action = $this->action;
        $results = array();

        foreach ($this->filterPlugins($arg) as $plug) {
            array_push($results, $plug->$action($arg));
        }

        return $results;
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
