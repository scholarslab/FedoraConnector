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


require_once __DIR__ . '/AbstractDisseminator.php';


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
            $disseminatorDir = __DIR__ . '/../../Disseminators/';
        }

        $this->disseminatorDir = $disseminatorDir;
        $this->disseminators = $this->_discoverDisseminators();
    }

    /**
     * This walks the disseminator directory and returns the classes for the 
     * disseminators.
     *
     * @return array The list of disseminator classes.
     */
    private function _discoverDisseminators() {
        $dir = new DirectoryIterator($this->disseminatorDir);

        $fileList = array();
        foreach ($dir as $file) {
            if ($file->isFile() && !$file->isDot()) {
                $filename = $file->getFilename();
                $pathname = $file->getPathname();

                $matches = array();
                if (preg_match('/^(\d+\W*)?(\w+)\.php$/', $filename, $matches)) {
                    require_once($pathname);
                    $className = "{$matches[2]}_Disseminator";
                    $fileList[$filename] = new $className();
                }
            }
        }

        ksort($fileList);
        $dissList = array_values($fileList);

        return $dissList;
    }

    /**
     * This returns the list of disseminator classes found.
     *
     * @return array A list of disseminator classes in order of use.
     */
    function getDisseminators() {
        return $this->disseminators;
    }

    /**
     * This tests whether a disseminator is installed that can handle the MIME 
     * type and datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     * @param boolean      $isPreview  Is this for a preview? The default is 
     * no.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function hasDisseminatorFor($mime, $datastream, $isPreview=false) {
        return (
            $this->getDisseminator($mime, $datastream, $isPreview) !== null
        );
    }

    /**
     * This tests whether a disseminator can handle displaying a datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function canHandle($mime, $datastream) {
        return $this->hasDisseminatorFor($mime, $datastream, false);
    }

    /**
     * This tests whether a disseminator can handle previewing a datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if the datastream can be disseminated.
     */
    function canPreview($mime, $datastream) {
        return $this->hasDisseminatorFor($mime, $datastream, true);
    }

    /**
     * This returns the first disseminator that says it can handle the 
     * datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     * @param boolean      $isPreview  Is this for a preview? The default is 
     * no.
     *
     * @return FedoraConnector_AbstractDisseminator|null The disseminator that 
     * can handle the input datastream.
     */
    function getDisseminator($mime, $datastream, $isPreview=false) {
        $predicate = ($isPreview) ? 'canPreview' : 'canHandle';

        foreach ($this->getDisseminators() as $diss) {
            if ($diss->$predicate($mime, $datastream)) {
                return $diss;
            }
        }

        return null;
    }

    /**
     * This disseminates a datastream.
     *
     * If no datastream currently installed can handle this, it returns null.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the disseminator.
     */
    function handle($mime, $datastream) {
        $diss = $this->getDisseminator($mime, $datastream, false);
        if ($diss === null) {
            return null;
        } else {
            return $diss->handle($mime, $datastream);
        }
    }

    /**
     * This disseminates a preview for a datastream.
     *
     * If no datastream can preview this, it returns null.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the disseminator.
     */
    function preview($mime, $datastream) {
        $diss = $this->getDisseminator($mime, $datastream, true);
        if ($diss === null) {
            return null;
        } else {
            return $diss->preview($mime, $datastream);
        }
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
