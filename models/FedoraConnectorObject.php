<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorObject extends Omeka_Record_AbstractRecord
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
        return get_record_by_id('FedoraConnectorServer', $this->server_id);
    }


    /**
     * Get parent item.
     *
     * @return Omeka_Record The item.
     */
    public function getItem()
    {
        return get_record_by_id('Item', $this->item_id);
    }


    /**
     * Construct the object base URL.
     *
     * @return string The URL.
     */
    public function getBaseUrl()
    {
        $s = $this->getServer();
        return "{$s->url}/{$s->getService()}/{$this->pid}/datastreams";
    }


    /**
     * Build the URL for the XML output for a given dsid.
     *
     * @param string $dsid The dsid to load.
     * @return string The URL for the datastream.
     */
    public function getMetadataUrl($dsid)
    {
        return "{$this->getBaseUrl()}/{$dsid}/content";
    }


}
