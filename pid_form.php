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
 * @copyright   2010 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 * @version     $Id$
 * @link        http://omeka.org/add-ons/plugins/FedoraConnector/
 * @tutorial    tutorials/omeka/FedoraConnector.pkg
 */

// XXX -> libraries/FedoraConnector/Viewer/PidForm.php

require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/fedora_utils.php';

/**
 * This returns the connector PID form.
 *
 * @param Omeka_Record $item The current item instance.
 *
 * @return string The PID form.
 */
function fedora_connector_pid_form($item)
{
    $webRoot = html_escape(WEB_ROOT);
    $deleteUrl = "$webRoot/admin/fedora-connector/datastreams/delete/";
    $addUrl = "$webRoot/admin/fedora-connector/datastreams/";

    $db = get_db();
    $datastreams = $db
        ->getTable('FedoraConnectorDatastream')
        ->findBySql('item_id = ?', array($item->id));

    /* XXX: I don't know what this block does. This function is only called 
     * from the admin_items_form_tabs hook function
     * fedora_connector_item_form_tabs, and $ht hasn't been initialized yet.
     * I'm going to replace this with a statement to initialize $ht (below), 
     * comment this out, and see what breaks.

    ob_start();
    $ht .= ob_get_contents();
    ob_end_clean();
     */
    $ht = '';

    $ht .= '<div id="omeka-map-form">';

    if ($datastreams !== null && $datastreams[0]->pid != NULL) {
        // If there are datastreams, display the table.
        $ht .= '<table><thead>'
            . '<th>ID</th><th>PID</th><th>Datastream ID</th>'
            . '<th>mime-type</th><th>Object Metadata</th>'
            . '<th>Preview</th><th>Delete?</th></thead>';

        foreach ($datastreams as $datastream) {
            $ht .= fedora_connector_pid_form_ds_row($datastream, $deleteUrl);
        }

        $ht .= '</table>';
        $ht .= "<p><a href='$addUrl?id={$item->id}'>Add another</a>?</p>";

    } else {
        // Otherwise, link to add a new datastream.
        $ht .= '<p>There are no Fedora datastreams associated with this item. '
           . "Why don't you <a href='$addUrl?id={$item->id}'>add one</a>?</p>";
    }

    $ht .= '</div>';
    return $ht;
}

/**
 * This creates the row for one datastream.
 *
 * @param Omeka_Record $dataStream The record for a data stream item.
 * @param string $deleteUrl The URL for deleting datastreams.
 *
 * @return string The row for this item in the PID form table.
 */
function fedora_connector_pid_form_ds_row($dataStream, $deleteUrl)
{
    $preview = strstr($dataStream['mime_type'], 'image/')
        ? render_fedora_datastream_preview($dataStream)
        : '';
    $importerLink = fedora_connector_importer_link($dataStream);

    $ht .= "<tr><td>{$dataStream->id}"
        . "</td><td>{$dataStream->pid}"
        . '</td><td>' . link_to_fedora_datastream($dataStream->id)
        . "</td><td>{$dataStream->mime_type}"
        . "</td><td>{$dataStream->metadata_stream} $importerLink"
        . "</td><td>$preview"
        . "</td><td><a href='$deleteUrl?id={$dataStream->id}'>"
        . 'Delete</a></td></tr>';
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
