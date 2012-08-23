<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Copied with modifications from https://github.com/rondobley/Zend-Framework-Validate-URL.
 *
 */

class FedoraConnector_Validate_IsUrl extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
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
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the $value is a valid url that starts with http(s)://
     * and the hostname is a valid TLD
     *
     * @param  string $value
     * @throws Zend_Validate_Exception if a fatal error occurs for validation process
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID_URL);
             return false;
        }

        $this->_setValue($value);

        // Get a Zend_Uri_Http object for our URL, this will only accept http(s) schemes.
        try {
            $uriHttp = Zend_Uri_Http::fromString($value);
        } catch (Zend_Uri_Exception $e) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        $hostnameValidator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_LOCAL);
        if (!$hostnameValidator->isValid($uriHttp->getHost())) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;

    }

}
