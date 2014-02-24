<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Copied with modifications from:
 * https://github.com/rondobley/Zend-Framework-Validate-URL
 */
class FedoraConnector_Validate_IsUrl extends Zend_Validate_Abstract
{


    /**
     * Error codes
     */
    const INVALID_URL = 'invalidUrl';


    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_URL   => "'%value%' is not a valid URL.",
    );


    /**
     * Returns true if the value is a valid url that starts with http(s)://
     * and the hostname is a valid TLD.
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {

        // Invalid if not string.
        if (!is_string($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        $this->_setValue($value);

        try {

            // Try to parse a URL.
            $uriHttp = Zend_Uri_Http::fromString($value);

        } catch (Zend_Uri_Exception $e) {

            // Invalid if not URL.
            $this->_error(self::INVALID_URL);
            return false;

        }

        $hostnameValidator = new Zend_Validate_Hostname(
            Zend_Validate_Hostname::ALLOW_LOCAL
        );

        // Allow local URLs.
        if (!$hostnameValidator->isValid($uriHttp->getHost())) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;

    }


}
