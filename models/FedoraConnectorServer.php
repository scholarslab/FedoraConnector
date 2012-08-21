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
 * Record class for servers.
 */
class FedoraConnectorServer extends Omeka_record
{

    public $name;
    public $url;
    public $is_default;

    /**
     * Retrieve the server version.
     *
     * @return string The version.
     */
    public function getVersion()
    {

        $version = fedorahelpers_getQueryNodes(
            "{$this->url}/describe?xml=true",
            "//*[local-name() = 'repositoryVersion']"
          );

        return $version != false ? $version->item(0)->nodeValue : false;

    }

    /**
     * Retrieve the server service (get or objects) for url construction.
     *
     * @return string The version.
     */
    public function getService()
    {

        if (preg_match('/^2\./', $this->getVersion())) {
            $service = 'get';
        } else {
            $service = 'objects';
        }

        return $service;

    }

    /**
     * Test to see if server is online.
     *
     * @return boolean True if online.
     */
    public function isOnline()
    {

        return ($this->getVersion() != '') ? true : false;

    }

    /**
     * Retrieve datastream nodes.
     *
     * @param $pid The pid to hit.
     *
     * @return array The nodes.
     */
    public function getDatastreamNodes($pid)
    {

        $nodes = fedorahelpers_getQueryNodes(
            "{$this->url}/objects/$pid/datastreams?format=xml",
            "//*[local-name() = 'datastream']"
        );

        return $nodes != false ? $nodes : false;

    }

    /**
     * Retrieve the mimeType for a datastream.
     *
     * @param $pid The pid to hit.
     * @param $datastream The datastream to query on.
     *
     * @return string The mimeType.
     */
    public function getMimeType($pid, $datastream)
    {

        $stream = fedorahelpers_getQueryNodes(
            "{$this->url}/objects/$pid/datastreams?format=xml",
            "//*[local-name() = 'datastream'][@dsid='" . $datastream . "']"
        );

        return $stream != false ? $stream->item(0)->getAttribute('mimeType') : false;

    }

    /**
     * Manage unique `is_default` field on save.
     *
     * @return void.
     */
    public function save()
    {

        // Get the current active server.
        $serversTable = $this->getTable('FedoraConnectorServer');
        $active = $serversTable->getActiveServer();

        // Is active set to true?
        if ($this->is_default == 1) {

            // Is the current active non-self?
            if ($active && $active->id !== $this->id) {

                // Switch active server.
                $active->is_default = 0;
                $active->parentSave();

            }

        }

        // If there is no current active server, set self active.
        else if (!$active) {
            $this->is_default = 1;
        }

        // Call parent.
        parent::save();

    }

    /**
     * Direct save.
     *
     * @return void.
     */
    public function parentSave()
    {
        parent::save();
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
