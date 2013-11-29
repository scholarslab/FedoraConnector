<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnector_Import
{


    /**
     * Set importer directory, instantiate plugins class.
     *
     * @param string $importerDir The directory containing the importers.
     */
    public function __construct($importerDir = null)
    {

        $this->importerDir = isset($importerDir) ?
            $importerDir :
            FEDORA_DIR . '/importers';

        $this->plugins = new FedoraConnector_Plugins(
            $this->importerDir,
            'Importer',
            'canImport',
            'import'
        );

    }


    /**
     * Import an object.
     *
     * @param Omeka_Record $object The object record.
     * @return array $results dsid => boolean (true if datastream handled).
     */
    public function import($object) {

        foreach (explode(',', $object->dsids) as $dsid) {

            // Try to get an importer.
            $importer = $this->getImporter($dsid);

            // If importer, do import.
            if (!is_null($importer)) {
                $importer->import($object, $dsid);
            }

        }

    }


    /**
     * Try to find an importer for the datastream.
     *
     * @param Omeka_Record $dsid The datastream dsid.
     * @return FedoraConnector_BaseImporter|null The importer, if one exists.
     */
    public function getImporter($dsid) {
        return $this->plugins->getPlugin($dsid);
    }


    /**
     * This returns the importer plugins loaded.
     *
     * @return array The list of importer plugin objects.
     */
    public function getImporters() {
        return $this->plugins->getPlugins();
    }


    /**
     * This tests whether a datastream can be imported by any importer plugins.
     *
     * @param Omeka_Record $datastream The datastream record.
     *
     * @return bool True if a plugin can import the metadata for this 
     * datastream.
     */
    public function canImport($datastream) {
        return $this->getImporter($datastream) !== null;
    }


}
