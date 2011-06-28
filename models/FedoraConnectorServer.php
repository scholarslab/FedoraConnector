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

        return fedorahelpers_getQueryNode(
            "{$this->url}describe?xml=true",
            "//*[local-name() = 'repositoryVersion']"
        );

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

        return fedorahelpers_getQueryNodes(
            "{$this->url}objects/$pid/datastreams?format=xml",
            "//*[local-name() = 'datastream']"
        );

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
            "{$this->url}objects/$pid/datastreams?format=xml",
            "//datastream[@dsid='DC']"
        );

        return $stream->getAttribute('mimeType');

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
