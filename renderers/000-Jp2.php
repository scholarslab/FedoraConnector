<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class Jp2_Renderer extends FedoraConnector_AbstractRenderer
{


    /**
     * Check of the renderer can handle a given mime type.
     *
     * @param string $mimeType The mimeType.
     * @return boolean True if this can display the datastream.
     */
    function canDisplay($mimeType)
    {
        return $mimeType == 'image/jp2';
    }


    /**
     * Displays an object.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @return DOMDocument The HTML DOM for the datastream.
     */
    function display($object, $params = array())
    {
        return $this->_display($object, $params);
    }


    /**
     * Displays the image.
     *
     * @param Omeka_Record $object The Fedora object record.
     * @param string $size The size to scale the image to.
     *
     * @return DOMDocument The HTML DOM for the image.
     */
    private function _display($object, $params = array())
    {
        if (empty($params))
            $params = array('scale' => '400,0');

        $server = $object->getServer();

        $url = "{$server->url}/{$server->getService()}/{$object->pid}" .
            "/methods/djatoka:jp2SDef/getRegion?" . http_build_query($params);

        $dom  = new DOMDocument();
        $node = $dom->createElement('img');
        $dom ->appendChild($node);
        $node->setAttribute('class', 'fedora-renderer');
        $node->setAttribute('alt', 'image');
        $node->setAttribute('src', $url);


        return $dom;
    }


}
