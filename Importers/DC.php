<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * DC importer.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class DC_Importer extends FedoraConnector_AbstractImporter
{

    /**
     * Checks to see if the datastream is DC format.
     *
     * @param object $dsic The dsid.
     *
     * @return boolean True if format is DC.
     */
    public function canImport($dsid)
    {
        return ($dsid == 'DC');
    }

    /**
     * Get xpath queries to extract node for a given DC element.
     *
     * @param string $name The DC name of the element.
     *
     * @return array The array of xpath queries.
     */
    public function getQueries($name)
    {
        return array('//*[local-name() = "' . strtolower($name) . '"]');
    }

}
