<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Datastream row manager.
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
class FedoraConnectorDatastream extends Omeka_record
{

    public $item_id;
    public $server_id;
    public $pid;
    public $dsid;
    public $stream;

    /**
     * This returns the datastream's base URL.
     *
     * This gets the server URL from the datastream's server_id. It also must query
     * the Fedora server to determine its version. Fedora 2 uses 'get' in the URL,
     * while Fedora 3 uses 'objects.'
     *
     * @param Omeka_Record $datastream The data stream to return the base URL for.
     *
     * @return string The base URL.
     */
    public function getBaseUrl()
    {

        $server = $this->getTable('FedoraConnectorServer')
            ->find($this->server_id);

        $url = "{$server->url}{$server->getService()}/{$this->pid}/datastreams/";
        return $url;

    }

    /**
     * This returns the datastream's URL.
     *
     * @return string The URL.
     */
    public function getUrl()
    {
        return $this->getBaseUrl() . $this->dsid;
    }

    /**
     * This returns the URL for the object metadata datastream.
     *
     * @param Omeka_Record $datastream The datastream.
     *
     * @return string The URL for the datastream.
     */
    public function getServerService()
    {
        return $this->getServer()->getService();
    }

    /**
     * This returns the URL for the object metadata datastream.
     *
     * @param Omeka_Record $datastream The datastream.
     *
     * @return string The URL for the datastream.
     */
    public function getMetadataUrl()
    {
        $baseUrl = $this->getBaseUrl();
        return "{$baseUrl}{$this->stream}/content";
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
        return "{$baseUrl}{$this->dsid}/content";
    }

    /**
     * Get server.
     *
     * @return object The server.
     */
    public function getServer()
    {
        return $this->getTable('FedoraConnectorServer')->find($this->server_id);
    }

    /**
     * Fetches the datastream node from Fedora.
     *
     * @return object The node.
     */
    public function getNode()
    {

        $server = $this->getServer();
        return fedorahelpers_getQueryNodes(
             "{$server->url}objects/$this->pid/datastreams?format=xml",
             "//*[local-name() = 'datastream'][@dsid='" . $this->dsid . "']"
        )->item(0);

    }

}


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
