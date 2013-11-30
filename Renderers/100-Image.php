<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class Image_Renderer extends FedoraConnector_AbstractRenderer
{


    private static $_excludedFormats = array(
        'jp2', 'x-mrsid-image'
    );


    /**
     * Check of the renderer can handle a given mime type.
     *
     * @param string $mimeType The mimeType.
     * @return boolean True if this can display the datastream.
     */
    function canDisplay($mimeType) {
        $exclude = implode('|', self::$_excludedFormats);
        return (bool)(preg_match('/^image\/(?!'.$exclude.')/', $mimeType));
    }


    /**
     * Display an object.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @return string The display HTML for the datastream.
     */
    function display($object, $params = array()) {

        // Construct the image URL.
        $url = "{$object->getServer()->url}/objects/{$object->pid}" . 
            "/datastreams/SCREEN/content";

        // Return the image tag.
        return "<img class='fedora-renderer' alt='image' src='{$url}' />";

    }


}
