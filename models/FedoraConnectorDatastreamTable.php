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
 * Table class for datastreams.
 */
class FedoraConnectorDatastreamTable extends Omeka_Db_Table
{

    /**
     * Returns datastreams for the main listing.
     *
     * @param string $order The constructed SQL order clause.
     * @param string $page The page.
     *
     * @return object The collections.
     */
    public function getDatastreams($page = null, $order = null)
    {

        $db = get_db();
        $select = $this->select()
            ->from(array('d' => $db->prefix . 'fedora_connector_datastreams'))
            ->joinLeft(array('s' => $db->prefix . 'fedora_connector_servers'), 'd.server_id = s.id')
            ->columns(array('server_name' => 's.name', 'datastream_id' => 'd.id', 'parent_item' =>
                "(SELECT text from `$db->ElementText` WHERE record_id = d.item_id AND element_id = 50)"));

        if (isset($page)) {
            $select->limitPage($page, get_option('per_page_admin'));
        }
        if (isset($order)) {
            $select->order($order);
        }

        return $this->fetchObjects($select);

    }

    /**
     * Inserts new datastream record.
     *
     * @param array $data Parameter array with:
     * $item_id
     * $server_id
     * $pid
     * $datastream
     * $mime_type
     * $metadataformat
     *
     * @return boolean True if insert succeeds.
     */
    public function createDatastream($data)
    {

        $datastream = new FedoraConnectorDatastream;
        $datastream->item_id = $data['item_id'];
        $datastream->server_id = $data['server_id'];
        $datastream->pid = $data['pid'];
        $datastream->datastream = $data['datastream'];
        $datastream->mime_type = $data['mime_type'];
        $datastream->metadata_stream = $data['metadataformat'];
        return $datastream->save() ? true : false;

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
