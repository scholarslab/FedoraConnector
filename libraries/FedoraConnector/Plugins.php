<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Plugins manager.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
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
     * @param mixed $arg An argument to pass to the predicate method.
     *
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
     * This tests for any plugin that passes the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate method.
     *
     * @return bool True if any plugin object passes the predicate call.
     */
    function hasPlugin($arg) {
        return ($this->getPlugin($arg) !== null);
    }

    /**
     * This calls an action on the first plugin object that passes the
     * predicate.
     *
     * @param mixed $arg An argument to pass to the predicate and action
     * methods.
     *
     * @return mixed This returns the result of calling the action on the first
     * plugin object that passes the predicate.
     */
    public function callFirst($arg)
    {

        $plugin = $this->getPlugin($arg);

        if ($plugin === null) {
            return null;
        }

        else {
            $action = $this->action;
            return $plugin->$action($arg);
        }

    }

    /**
     * This calls an action on all plugin objects that pass the predicate.
     *
     * @param mixed $arg An argument to pass to the predicate and action
     * methods.
     *
     * @return array This returns the result of calling the action on the
     * plugin objects that pass the predicate.
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
