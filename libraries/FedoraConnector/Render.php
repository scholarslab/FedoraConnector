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


require_once dirname(__FILE__) . '/AbstractRenderer.php';
require_once dirname(__FILE__) . '/Plugins.php';


/**
 * This class handles discovering and instatiating the various renderers 
 * (displayers) for the datastreams.
 *
 * Renderers are discovered from the files in the $rendererDir. By default, 
 * this is FedoraConnector/Renderers/. Files are loaded in sorted order, so you 
 * can establish a precedence for renderers by adding a three-digit number 
 * to the front of each file name, for example, "100-ImageJpeg.php."
 *
 * Each file in the renderer directory should have a class that implements 
 * FedoraConnector_AbstractRenderer with a name like 'FILENAME_Renderer'.  
 * (If the filename began with a numeric prefix and some punctuation, that's 
 * stripped off.) See the documentation for the abstract base class for 
 * information on what interface the renderer should publish.
 *
 * @package FedoraConnector
 * @subpackage Libraries
 */

class FedoraConnector_Render
{
    /**
     * Set renderer directory, instantiate plugins classes.
     *
     * @param string $rendererDir The directory containing the
     * renderers. Defaults to FedoraConnector/renderers.
     *
     * @return void.
     */
    public function __construct($rendererDir = null)
    {

        $this->$rendererDir = isset($rendererDir) ?
            $rendererDir :
            FEDORA_CONNECTOR_PLUGIN_DIR . '/renderers';

        $this->previewPlugins = new FedoraConnector_Plugins(
            $this->$rendererDir,
            'Renderer',
            'canPreview',
            'preview'
        );

        $this->displayPlugins = new FedoraConnector_Plugins(
            $this->$rendererDir,
            'Renderer',
            'canDisplay',
            'display'
        );

    }

    /**
     * Render a datastream.
     * If no datastream currently installed can handle this, it returns null.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the renderer.
     */
    public function display($datastream) {

        return $this->displayPlugs->callFirst($datastream);

    }

    /**
     * Render a preview for a datastream.
     * If no datastream can preview this, it returns null.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string|null The output of the renderer.
     */
    public function preview($datastream) {

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
