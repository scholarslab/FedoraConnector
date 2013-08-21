<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * image/* renderer.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
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
     *
     * @return boolean True if this can display the datastream.
     */
    function canDisplay($mimeType) {
        $excludeString = implode('|', self::$_excludedFormats);
        return (bool)(preg_match('/^image\/(?!'.$excludeString.')/', $mimeType));
    }

    /**
     * Display an object.
     *
     * @param Omeka_Record $object The Fedora object record.
     *
     * @return string The display HTML for the datastream.
     */
    function display($object, $params = array()) {
        $url = "{$object->getServer()->url}/objects/{$object->pid}/datastreams/SCREEN/content";
        return "<img class='fedora-renderer' alt='image' src='{$url}' />";
    }

}
