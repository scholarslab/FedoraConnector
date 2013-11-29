<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraGateway
{


    /**
     * Load DOMDocument for a URL.
     *
     * @param string $url The url of the document.
     *
     * @return DOMDocument The document.
     */
    public function load($url)
    {
        $xml = new DOMDocument();
        $xml->load($url);
        return $xml;
    }


    /**
     * Return nodes by url and xpath query.
     *
     * @param string $url The url of the document.
     * @param string $xpath The XPath query.
     *
     * @return object The matching nodes.
     */
    public function query($url, $xpath)
    {

        try {

            $xml = $this->load($url);
            $query = new DOMXPath($xml);
            $result = $query->query($xpath);

        } catch (Exception $e) {
            $result = false;
        }

        return $result;

    }


}
