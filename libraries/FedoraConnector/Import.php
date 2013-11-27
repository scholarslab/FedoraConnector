<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Top-level import executor.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class FedoraConnector_Import
{

    /**
     * Set importer directory, instantiate plugins class.
     *
     * @param string $importerDir The directory containing the
     * importers. Defaults to FedoraConnector/Importers.
     *
     * @return void.
     */
    public function __construct($importerDir = null)
    {

        $this->importerDir = isset($importerDir) ?
            $importerDir :
            FEDORA_DIR . '/Importers';

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
     *
     * @return array $results Array of dsid => boolean (true if the
     * datastream was handled).
     */
    public function import($object) {

        // Walk datastreams.
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
     *
     * @return FedoraConnector_BaseImporter|null If an importer can be found,
     * it's returned; otherwise, null.
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
        return ($this->getImporter($datastream) !== null);
    }

}
