<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * JPEG-2000 renderer.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class Jp2_Renderer extends FedoraConnector_AbstractRenderer
{

    /**
     * This constructs an instance.
     */
    function __construct()
    {
        $this->sizes = array(
            'thumb'  => 80,
            'screen' => 600,
            '*'      => 400
        );
    }

    /**
     * Check of the renderer can handle a given mime type.
     *
     * @param string $mimeType The mimeType.
     *
     * @return boolean True if this can display the datastream.
     */
    function canDisplay($mimeType)
    {
        return ($mimeType == 'image/jp2');
    }

    /**
     * This tests whether this renderer can preview a datastream.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return boolean True if this can display the datastream.
     */
    function canPreview($datastream)
    {
        return $this->canDisplay($datastream);
    }

    /**
     * This displays an object.
     *
     * @param Omeka_Record $object The Fedora object record.
     *
     * @return string The display HTML for the datastream.
     */
    function display($object)
    {
        return $this->_display($object, '*');
    }

    /**
     * This displays a datastream's preview.
     *
     * @param Omeka_Record $datastream The data stream.
     *
     * @return string The preview HTML for the datastream.
     */
    function preview($datastream)
    {
        return $this->_display($datastream, 'thumb');
    }

    /**
     * Displays the image.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @param string $size The size to scale the image to.
     *
     * @return string The HTML for the image.
     */
    private function _display($object, $size='*') 
    {

        // Get size.
        if (array_key_exists($size, $this->sizes)) {
            $px = $this->sizes[$size];
        } else {
            $px = $this->sizes['*'];
        }

        // Get server.
        $server = $object->getServer();

        // Construct image URL.
        $url = "{$server->url}/{$server->getService()}/{$object->pid}"
            . "/methods/djatoka:jp2SDef/getRegion?scale={$px},{$px}";

        // Construct HTML.
        $html = "<img alt='image' src='{$url}' />";
        return $html;

    }

}
