<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Object row manager.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


/**
 * Record class for datastreams.
 */
class FedoraConnectorObject extends Omeka_record
{


    /**
     * The id of the parent item [integer].
     */
    public $item_id;

    /**
     * The id of the parent server [integer].
     */
    public $server_id;

    /**
     * The Fedora object pid [string].
     */
    public $pid;

    /**
     * A comma-delimited list of dsids [string].
     */
    public $dsids;


    /**
     * Get parent server.
     *
     * @return Omeka_Record The server.
     */
    public function getServer()
    {
        return $this->getTable('FedoraConnectorServer')->find($this->server_id);
    }

    /**
     * Get parent item.
     *
     * @return Omeka_Record The item.
     */
    public function getItem()
    {
        return $this->getTable('Item')->find($this->item_id);
    }

    /**
     * Construct the object base URL.
     *
     * @return string The URL.
     */
    public function getBaseUrl()
    {
        $server = $this->getServer();
        return "{$server->url}/{$server->getService()}/{$this->pid}/datastreams";
    }

    /**
     * Build the URL for the XML output for a given dsid.
     *
     * @param string $dsid The dsid to load.
     *
     * @return string The URL for the datastream.
     */
    public function getMetadataUrl($dsid)
    {
        $baseUrl = $this->getBaseUrl();
        return "{$baseUrl}/{$dsid}/content";
    }

    /**
     * Return the URL for the datastream content.
     *
     * @param Omeka_Record $datastream The datastream.
     *
     * @return string The URL for the datastream.
     */
    public function getContentUrl()
    {
        $baseUrl = $this->getBaseUrl();
        return "{$baseUrl}/{$this->dsid}/content";
    }

}
