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
 * @package     FedoraConnector
 * @subpackage  Libraries
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


require_once dirname(__FILE__) . '/AbstractDisseminator.php';
require_once dirname(__FILE__) . '/PluginDir.php';


/**
 * This class handles discovering and instatiating the various disseminators 
 * (displayers) for the datastreams.
 *
 * Disseminators are discovered from the files in the $disseminatorDir. By 
 * default, this is FedoraConnector/Disseminators/. Files are loaded in sorted 
 * order, so you can establish a precedence for disseminators by adding a 
 * three-digit number to the front of each file name, for example, 
 * "100-ImageJpeg_Disseminator.php."
 *
 * Each file in the disseminator directory should have a class that implements 
 * FedoraConnector_AbstractDisseminator with a name like 
 * 'FILENAME_Disseminator'.  (If the filename began with a numeric prefix and 
 * some punctuation, that's stripped off.) See the documentation for the 
 * abstract base class for information on what interface the disseminator 
 * should publish.
 *
 * @package FedoraConnector
 * @subpackage Libraries
 */
class FedoraConnector_Disseminators
{
    //{{{properties

    /**
     * The directory containing the disseminators.
     *
     * @var string
     */
    var $disseminatorDir;

    /**
     * This is the plugin manager for previews.
     *
     * @var FedoraConnector_PluginDir
     */
    var $previewPlugs;

    /**
     * This is the plugin manager for displays.
     *
     * @var FedoraConnector_PluginDir
     */
    var $displayPlugs;

    /**
     * The list of disseminator classes in the order they should be loaded.
     *
     * @var array
     */
    var $disseminators;

    //}}}

    /**
     * This constructs an instance of FedoraConnector_Disseminators.
     *
     * @param string $disseminatorDir This is the directory containing the 
     * disseminators. It defaults to FedoraConnector/Disseminators.
     */
    function __construct($disseminatorDir=null) {
        if ($disseminatorDir === null) {
            $disseminatorDir = dirname(__FILE__) . '/../../Disseminators/';
        }

        $this->disseminatorDir = $disseminatorDir;

        $this->previewPlugs = new FedoraConnector_PluginDir(
            $disseminatorDir,
            'Disseminator',
            'canPreview',
            'preview'
        );
        $this->displayPlugs = new FedoraConnector_PluginDir(
            $disseminatorDir,
            'Disseminator',
            'canDisplay',
            'display'
        );
    }

    /**
     * This returns the list of disseminator classes found.
     *
     * @return array A list of disseminator classes in order of use.
     */
    function getDisseminators() {
        return $this->displayPlugs->getPlugins();
    }

    /**
     * This tests whether a disseminator is installed that can handle the MIME 
     * type and datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     * @param boolean      $isPreview  Is this for a preview? The default is 
     * no.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function hasDisseminatorFor($datastream, $isPreview=false) {
        $plugs = ($isPreview) ? $this->previewPlugs : $this->displayPlugs;
        return $plugs->hasPlugin($datastream);
    }

    /**
     * This tests whether a disseminator can handle displaying a datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function canDisplay($datastream) {
        return $this->hasDisseminatorFor($datastream, false);
    }

    /**
     * This tests whether a disseminator can handle previewing a datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function canPreview($datastream) {
        return $this->hasDisseminatorFor($datastream, true);
    }

    /**
     * This returns the first disseminator that says it can handle the 
     * datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     * @param boolean      $isPreview  Is this for a preview? The default is 
     * no.
     *
     * @return FedoraConnector_AbstractDisseminator|null The disseminator that 
     * can handle the input datastream.
     */
    function getDisseminator($datastream, $isPreview=false) {
        $plugs = ($isPreview) ? $this->previewPlugs : $this->displayPlugs;
        return $plugs->getPlugin($datastream);
    }

    /**
     * This disseminates a datastream.
     *
     * If no datastream currently installed can handle this, it returns null.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the disseminator.
     */
    function display($datastream) {
        return $this->displayPlugs->callFirst($datastream);
    }

    /**
     * This disseminates a preview for a datastream.
     *
     * If no datastream can preview this, it returns null.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the disseminator.
     */
    function preview($datastream) {
        return $this->previewPlugs->callFirst($datastream);
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
