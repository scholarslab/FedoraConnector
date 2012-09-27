<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Gateway class to Fedora repository.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
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
