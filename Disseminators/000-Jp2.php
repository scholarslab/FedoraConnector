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


/**
 * This class defines a display adapter for an image.
 */
class Jp2_Disseminator extends FedoraConnector_AbstractDisseminator
{
    /**
     * This contains the sizes for image rescaling.
     *
     * @var array
     */
    var $sizes;

    /**
     * This constructs an instance.
     */
    function __construct() {
        $this->sizes = array(
            'thumb'  => 120,
            'screen' => 600,
            '*'      => 400
        );
    }

    /**
     * This tests whether this disseminator can display a datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if this can display the datastream.
     */
    function canHandle($mime, $datastream) {
        return ($mime == 'image/jp2');
    }

    /**
     * This tests whether this disseminator can preview a datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if this can display the datastream.
     */
    function canPreview($mime, $datastream) {
        return $this->canHandle($mime, $datastream);
    }

    /**
     * This displays a datastream.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string The display HTML for the datastream.
     */
    function handle($mime, $datastream) {
        $html = $this->_display($datastream, '*');
        return $html;
    }

    /**
     * This displays a datastream's preview.
     *
     * @param string       $mime       The data stream's MIME type.
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string The preview HTML for the datastream.
     */
    function preview($mime, $datastream) {
        $html = $this->_display($datastream, 'thumb');
        return $html;
    }

    /**
     * This displays the image.
     *
     * @param Omeka_Record $datastream The data stream.
     * @param string       $size       The size to scale the image to.
     *
     * @return string The HTML for the datastream.
     */
    private function _display($datastream, $size='*') {
        if (array_key_exists($size, $this->sizes)) {
            $px = $this->sizes[$size];
        } else {
            $px = $this->sizes['*'];
        }

        $server = $datastream->getServer();
        $url = "{$server}get/{$datastream->pid}"
            . "/djatoka:jp2SDef/getRegion?scale={$px},{$px}";

        $html = "<img alt='image' src='{$url}' />";
        return $html;
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
