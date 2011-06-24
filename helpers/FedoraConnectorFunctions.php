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
 * A homebrew colum sorter, implemented so as to keep more control
 * over how the record loop is handled in the view.
 *
 * @param object $request The incoming request dispatched by the 
 * front controller.
 *
 * @return string $order The sorting parameter for the query.
 */
function fedorahelpers_doColumnSortProcessing($sort_field, $sort_dir)
{

    if (isset($sort_dir)) {
        $sort_dir = ($sort_dir == 'a') ? 'ASC' : 'DESC';
    }

    return (isset($sort_field)) ? trim(implode(' ', array($sort_field, $sort_dir))) : '';

}

/**
 * Return nodes by file and xpath query.
 *
 * @param string $uri The uri of the document.
 * @param string $xpath The XPath query.
 *
 * @return object The matching nodes.
 */
function fedorahelpers_getQueryNodes($uri, $xpath)
{

    $xml = new DomDocument();
    $xml->load($uri);
    $query = new DOMXPath($xml);
    return $query->query($xpath);

}

/**
 * Same as getQueryNodes, but pulls out single result.
 *
 * @param string $uri The uri of the document.
 * @param string $xpath The XPath query.
 *
 * @return object The matching node.
 */
function fedorahelpers_getQueryNode($uri, $xpath)
{

    $xml = new DomDocument();
    $xml->load($uri);
    $query = new DOMXPath($xml);
    $nodes = $query->query($xpath);

    $node = null;
    foreach ($nodes as $n) {
        $node = $n->nodeValue;
    }

    return $node;

}

/**
 * Retrieves items to populate the listings in the itemselect view.
 *
 * @param string $page The page to fetch.
 * @param string $order The constructed SQL order clause.
 * @param string $search The string to search for.
 *
 * @return array $items The items.
 */
function fedorahelpers_getItems($page, $order, $search)
{

    $db = get_db();
    $itemTable = $db->getTable('Item');

    // Wretched query. Fallback from weird issue with left join where item id was
    // getting overwritten. Fix.
    $select = $db->select()
        ->from(array('item' => $db->prefix . 'items'))
        ->columns(array('item_id' => 'item.id', 
            'Type' =>
            "(SELECT name from `$db->ItemType` WHERE id = item.item_type_id)",
            'item_name' =>
            "(SELECT text from `$db->ElementText` WHERE record_id = item.id AND element_id = 50 LIMIT 1)",
            'creator' =>
            "(SELECT text from `$db->ElementText` WHERE record_id = item.id AND element_id = 39)"
            ));

    if (isset($page)) {
        $select->limitPage($page, get_option('per_page_admin'));
    }
    if (isset($order)) {
        $select->order($order);
    }
    if (isset($search)) {
        $select->where("(SELECT text from `$db->ElementText` WHERE record_id = item.id AND element_id = 50) like '%" . $search . "%'");
    }

    return $itemTable->fetchObjects($select);

}

/**
 * Format item add date for datastream create workflow.
 *
 * @param string $date The date in datetime.
 *
 * @return string $date The formatted date.
 */
function fedorahelpers_formatDate($date)
{

    $date = new DateTime($date);
    return '<strong>' . $date->format('F j, Y') . '</strong> at ' .
       $date->format('g:i a');

}

/**
 * Check to see if a datastream is omitted.
 *
 * @param array $datastream The datastream.
 *
 * @return boolean True if the datastream is omitted.
 */
function fedorahelpers_isOmittedDatastream($datastream)
{

    $omittedStreams = explode(',', get_option('fedora_connector_omitted_datastreams'));
    return in_array($datastream->getAttribute('dsid'), $omittedStreams) ? true : false;

}

/**
 * Build the form for the Edit Item menu.
 *
 * @param object $item The item being edited.
 *
 * @return string The form.
 */
function fedorahelpers_doItemFedoraForm($item)
{

    $db = get_db();
    $select = $db->getTable('FedoraConnectorDatastream')->select()
        ->from(array('d' => $db->prefix . 'fedora_connector_datastreams'))
        ->joinLeft(array('s' => $db->prefix . 'fedora_connector_servers'), 'd.server_id = s.id')
        ->columns(array('server_name' => 's.name', 'datastream_id' => 'd.id', 'parent_item' =>
            "(SELECT text from `$db->ElementText` WHERE record_id = d.item_id AND element_id = 50 LIMIT 1)"))
        ->where('d.item_id = ' . $item->id);

    $datastreams = $db->getTable('FedoraConnectorDatastream')->fetchObjects($select);

    $form = '';

    if (count($datastreams) == 0) {
        $form .= '<p>There are no datastreams yet. <a href="' . uri('/fedora-connector/datastreams/create/item/' . $item->id . '/pid') . '">Add one</a>.</p>';
    }

    else {
        $form .= '<table><thead><th>Datastream</td><th>PID</td><th>Server</td><th>Metadata Format</td><th>Actions</td>';
        foreach ($datastreams as $datastream) {
            $form .= '<tr>
                <td>' . $datastream->datastream . '</td>
                <td>' . $datastream->pid . '</td>
                <td><a href="' . uri('/fedora-connector/servers/edit/' . $datastream->server_id) . '">' . $datastream->server_name . '</a></td>
                <td>' . $datastream->metadata_stream . '</td>
                <td><a href="' . uri('/fedora-connector/datastreams/' . $datastream->datastream_id . '/import') . '"><strong>Import</strong></a></td>
                </tr>';
        }
        $form .= '</table>';
        $form .= '<p><strong><a href="' . uri('/fedora-connector/datastreams/create/item/' . $item->id . '/pid') . '">Add another datastream -></a></strong></p>';
    }

    return $form;

}





    // $db = get_db();
    // $datastreams = $db->getTable('FedoraConnector_Datastream')->findBySql('item_id = ?', array($item->id));

    // ob_start();
    // $ht .= ob_get_contents();
    // ob_end_clean();
    
    // $ht .= '<div id="omeka-map-form">';
    // //if there are datastreams, display the table
    // if ($datastreams[0]->pid != NULL){
    //     $ht .= '<table><thead><th>ID</th><th>PID</th><th>Datastream ID</th><th>mime-type</th><th>Object Metadata</th><th>Preview</th><th>Delete?</th></thead>';
    //     foreach ($datastreams as $datastream){
    //         $deleteUrl = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/delete/';        
    //         $addUrl = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
    //         $ht.= '<tr><td>' . $datastream->id . '</td><td>' . $datastream->pid . '</td><td>' . link_to_fedora_datastream($datastream->id) . '</td><td>' . $datastream->mime_type . '</td><td>' . $datastream->metadata_stream . ' ' . fedora_connector_importer_link($datastream) . '</td><td>' . (strstr($datastream['mime_type'], 'image/') ? render_fedora_datastream_preview($datastream) : '') . '</td><td><a href="' . $deleteUrl . '?id=' . $datastream->id . '">Delete</a></td></tr>';
    //     }
    //     $ht .= '</table>';
    //     $ht .= '<p><a href="' . $addUrl . '?id=' . $item->id . '">Add another</a>?</p>';
    // } else {
    //     //otherwise link to add a new datastream
    //     $addUrl = html_escape(WEB_ROOT) . '/admin/fedora-connector/datastreams/';
    //     $ht .= '<p>There are no Fedora datastreams associated with this item.  Why don\'t you <a href="' . $addUrl . '?id=' . $item->id . '">add one</a>?</p>';
    // }    
    // $ht .= '</div>';
    // return $ht;
