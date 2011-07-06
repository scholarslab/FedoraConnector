<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * FedoraConnector Omeka plugin allows users to reuse content managed in
 * institutional repositories in their Omeka repositories.
 *
 * The FedoraConnector plugin provides methods to generate calls against Fedora-
 * based content disemminators. Unlike traditional ingestion techniques, this
 * plugin provides a facade to Fedora-Commons repositories and records pointers
 * to the "real" objects rather than creating new physical copies. This will
 * help ensure longer-term durability of the content streams, as well as allow
 * you to pull from multiple institutions with open Fedora-Commons
 * respositories.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      Ethan Gruber <ewg4x@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      Wayne Graham <wayne.graham@virginia.edu>
 * @author      Eric Rochester <err8n@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */
?>

<?php

/**
 * Record class for datastreams.
 */
class FedoraConnectorDatastream extends Omeka_record
{

    public $item_id;
    public $server_id;
    public $pid;
    public $datastream;
    public $mime_type;
    public $metadata_stream;

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

        if (preg_match('/^2\./', $server->getVersion())) {
            $service = 'get';
        } else {
            $service = 'objects';
        }

        $url = "{$server->url}{$service}/{$this->pid}/datastreams/";
        return $url; 

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
        return "{$baseUrl}{$this->metadata_stream}/content";

    }

    /**
     * This returns the URL for the object metadata datastream.
     *
     * @param Omeka_Record $datastream The datastream.
     *
     * @return string The URL for the datastream.
     */
    public function getContentUrl()
    {

        $baseUrl = $this->getBaseUrl();
        return "{$baseUrl}{$this->datastream}/content";

    }

    /**
     * Renders a preview of the datastream.
     *
     * @return string The preview markup.
     */
    public function renderPreview()
    {

        $renderer = new FedoraConnector_Render;

        if ($renderer->canPreview($this)) {
            return $renderer->preview($this);
        }

        else {
            return '<span style="color: gray; font-size: 0.8em;">[Unavailable]</span>';
        }

    }

    /**
     * Fetches the database object for the parent server.
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
             "//*[local-name() = 'datastream'][@dsid='" . $this->datastream . "']"
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
