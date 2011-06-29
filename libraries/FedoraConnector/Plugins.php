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


class FedoraConnector_Plugins
{

    /**
     * Set plugin format parameters.
     *
     * @param string $dirname The name of the directory to look for plugin 
     * files in.
     *
     * @param string $suffix The suffix to use for identifying plugin classes.
     *
     * @param string $filter The name of the plugin class method to use when
     * filtering the plugins.
     *
     * @param string $action The name of the plugin class method to use when
     * performing the plugin's action.
     *
     * @return void.
     */
    public function __construct($dirname, $suffix, $filter, $action)
    {

        $this->dirname = $dirname;
        $this->suffix = $suffix;
        $this->filter = $filter;
        $this->action = $action;

        $this->plugins = $this->_discover();

    }

    /**
     * Walk $dirname to find plugin classes.
     *
     * @return array The list of plugin objects.
     */
    private function _discover() {

        $dir = new DirectoryIterator($this->dirname);

        foreach ($dir as $file) {

            if ($file->isFile() && !$file->isDot()) {

                $filename = $file->getFilename();
                $pathname = $file->getPathname();

                if (preg_match('/^(\d+\W*)?(\w+)\.php$/', $filename, $matches)) {
                    require_once($pathname);
                    $class = "{$matches[2]}_{$this->suffix}";
                    $files[$filename] = new $class();
                }

            }

        }

        ksort($files);
        $plugins = array_values($files);

        return $plugins;

    }

    /**
     * Return the first plugin that passes the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate method.
     *
     * @return mixed This returns the first plugin object that passes the 
     * predicate.
     */
    function getPlugin($arg) {

        $predicate = $this->filter;

        foreach ($this->plugins as $plugin) {

            if ($plugin->$predicate($arg)) {
                return $plugin;
            }

        }

        return null;

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
