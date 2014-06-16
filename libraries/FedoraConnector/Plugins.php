<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_Plugins
{


    /**
     * Set plugin format parameters.
     *
     * @param string $dirname The directory to look for plugin files.
     * @param string $suffix The suffix to use for identifying plugin classes.
     * @param string $filter The method to use when filtering plugins.
     * @param string $action The method to use when executing plugin.
     */
    public function __construct($dirname, $suffix, $filter, $action)
    {
        $this->dirname  = $dirname;
        $this->suffix   = $suffix;
        $this->filter   = $filter;
        $this->action   = $action;
        $this->plugins  = $this->_discover();
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
     * @return mixed The first plugin object that passes the predicate.
     */
    function getPlugin($arg) {

        $predicate = $this->filter;

        foreach ($this->plugins as $plugin) {
            if ($plugin->$predicate($arg)) return $plugin;
        }

        return null;

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
     * Return all plugins that pass the filter predicate.
     *
     * @param mixed $arg An argument to pass to the predicate method.
     * @return array A list of the plugin objects that pass the predicate.
     */
    function filterPlugins($arg) {

        $predicate = $this->filter;

        $results = array();
        foreach ($this->plugins as $plugin) {
            if ($plugin->$predicate($arg)) {
                $results[] = $plugin;
            }
        }

        return $results;

    }


    /**
     * Test for any plugin that passes the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate method.
     * @return bool True if any plugin object passes the predicate call.
     */
    function hasPlugin($arg) {
        return ($this->getPlugin($arg) !== null);
    }


    /**
     * Call an action on the first plugin object that passes the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate/action methods.
     * @return mixed Result from the first plugin that passes the predicate.
     */
    public function callFirst($arg)
    {

        $plugin = $this->getPlugin($arg);
        if (is_null($plugin)) return;

        else {
            $action = $this->action;
            return $plugin->$action($arg);
        }

    }


    /**
     * This calls an action on all plugin objects that pass the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate/action methods.
     * @return array Results from all plugins objects that pass the predicate.
     */
    function callAll($arg) {

        $action = $this->action;

        $results = array();
        foreach ($this->filterPlugins($arg) as $plugin) {
            $results[] = $plugin->$action($arg);
        }

        return $results;

    }


}
