<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class DC_Importer extends FedoraConnector_AbstractImporter
{


    /**
     * Check to see if the datastream is Dublin Core.
     *
     * @param object $dsic The dsid.
     * @return boolean True if format is DC.
     */
    public function canImport($dsid)
    {
        return $dsid == 'DC';
    }


    /**
     * Get xpath queries to extract node for a given DC element.
     *
     * @param string $name The DC name of the element.
     * @return array The array of xpath queries.
     */
    public function getQueries($name)
    {
        return array('//*[local-name() = "' . strtolower($name) . '"]');
    }


}
