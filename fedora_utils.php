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

// XXX fix namespace

require_once dirname(__FILE__) . '/libraries/FedoraConnector/Renderers.php';
require_once dirname(__FILE__) . '/libraries/FedoraConnector/Importers.php';

/**
 * This returns 'active' when called.
 *
 * @return string This returns 'active'.
 */
function fedora_connector_installed()
{
    return 'active';
}

/**
 * This creates a link to a Fedora datastream.
 *
 * This is used on the Edit Item page under Fedora Datastreams tab.
 *
 * @param integer $id This is the
 *
 * @return string A link to the datastream associated with the item.
 */
function link_to_fedora_datastream($id)
{
    // XXX: -> libraries/FedoraConnector/Viewer/Datastream.php
    $db = get_db();
    $datastream = $db->getTable('FedoraConnector_Datastream')->find($id);
    $url = fedora_connector_content_url($datastream);

    $html = "<a href='$url' target='_blank'>{$datastream->datastream}</a>";

    return $html;
}

/**
 * This displays the Fedora datastreams.
 *
 * This is needed for the Admin Show Item page.
 *
 * @param Omeka_Item $item The item to return the data streams for.
 *
 * @return string The HTML for displaying a list of Fedora datastreams.
 */
function list_fedora_datastreams($item)
{
    // XXX -> models/FedoraConnector/Datastream.php (static method)
    // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    $db = get_db();
    $datastreams = $db
        ->getTable('FedoraConnector_Datastream')
        ->findBySql('item_id = ?', array($item->id));

    $html = '';
    if ($datastreams[0]->pid != NULL) {
        foreach ($datastreams as $datastream) {
            $link = link_to_fedora_datastream($datastream->id);
            $html .= "<h4>PID: {$datastream->pid}</h4>"
                . '<ul>'
                . "<li>Datastream: $link</li>"
                . "<li>Metadata: {$datastream->metadata_stream}</li>"
                . '</ul>';
        }

    } else {
        $link = link_to_item('Add a Datastream', array(), 'edit');
        $html .= "<p>There are no datastreams for this item yet. $link.</p>";
    }

    return $html;
}


/**
 * This returns the URL of the server.
 *
 * This gets the URL for the server_id in the $datastream.
 *
 * @param Omeka_Record $datastream This is the data stream to return the server
 * for.
 *
 * @return string The URL for the datastream's server.
 */
function fedora_connector_get_server($datastream)
{
    // XXX -> models/FedoraConnector/Datastream.php
    $server = get_db()
        ->getTable('FedoraConnector_Server')
        ->find($datastream->server_id)
        ->url;

    return $server;
}

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
function fedora_connector_datastream_base_url($datastream) {
    // XXX -> models/FedoraConnector/Datastream.php
    $server = get_db()
        ->getTable('FedoraConnector_Server')
        ->find($datastream->server_id);

    if (preg_match('/^2\./', $server->version)) {
        $service = 'get';
    } else {
        $service = 'objects';
    }

    $url = "{$server->url}{$service}/{$datastream->pid}/datastreams/";
    return $url;
}

/**
 * This returns the URL of the datastream.
 *
 * This gets the server URL from the datastream's server_id. It also must query
 * the Fedora server to determine its version. Fedora 2 uses 'get' in the URL,
 * while Fedora 3 uses 'objects.'
 *
 * @param Omeka_Record $datastream The data stream to return the URL for.
 *
 * @return string The URL for the datastream.
 */
function fedora_connector_content_url($datastream)
{
    // XXX -> models/FedoraConnector/Datastream.php
    $baseUrl = fedora_connector_datastream_base_url($datastream);
    return "{$baseUrl}{$datastream->datastream}/content";
}

/**
 * This returns the URL for the object metadata datastream.
 *
 * This gets the server URL from the datastream's server_id. It also much query
 * the Fedora server to determine its version. Fedora 2 uses 'get' in the URL,
 * Fedora 3 uses 'objects.'
 *
 * @param Omeka_Record $datastream The data stream to return the metadata URL
 * for.
 *
 * @return string The URL for the datastream.
 */
function fedora_connector_metadata_url($datastream)
{
    // XXX -> models/FedoraConnector/Datastream.php (->getMetadataUrl())
    $baseUrl = fedora_connector_datastream_base_url($datastream);
    return "{$baseUrl}{$datastream->metadata_stream}/content";
}

/**
 * This generates the A-tag link to import an object's metadata if the XML has
 * an importer function associated with it.
 *
 * If there is no importer function, it returns null.
 *
 * @param Omeka_Record $datastream The datastream to create the A-tag for.
 *
 * @return string The A-tag or null.
 */
function fedora_connector_importer_link($datastream)
{
    // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    $importUrl = html_escape(WEB_ROOT)
        . '/admin/fedora-connector/datastreams/import/';
    $importers = fedora_connector_list_importers();

    if (in_array($datastream->metadata_stream, $importers)) {
        return "[<a href='{$importUrl}?id={$datastream->id}'>import</a>]";
    } else {
        return null;
    }
}

/**
 * This renders a preview for the data stream.
 *
 * Currently, this only generates a preview for images.
 *
 * @param Omeka_Record $datastream The datastream to create the preview for.
 *
 * @return string An HTML string for the datastream.
 */
function render_fedora_datastream_preview($datastream)
{
    // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    $diss = new FedoraConnector_Renderers();
    return $diss->preview($datastream->mime_type, $datastream);
}

/**
 * This renders the datastream.
 *
 * Depending on the datastreams MIME-type, this renders differently. Currently,
 * this handles for these cases: image/jp2, image/jpeg, and text/xml (TEI).
 *
 * @param integer $id The ID of the datastream to render.
 * @param array $options An array of rendering options.
 *
 * @return string The HTML for the datastream.
 */
function render_fedora_datastream($id, $options=array())
{
    // XXX -> libraries/FedoraConnector/Viewer/Datastream.php
    $datastream = get_db()
        ->getTable('FedoraConnector_Datastream')
        ->find($id);
    $diss = new FedoraConnector_Renderers();
    return $diss->preview($datastream->mime_type, $datastream);
}

/**
 * This returns a list of the available importers.
 *
 * @return array An array listing the metadata formats that have import
 * functions.
 */
function fedora_connector_list_importers()
{
    // XXX -> libraries/FedoraConnector/Importers.php
    $imps = new FedoraConnector_Importers();
    return $imps->getImporters();
}

/**
 * This imports the metadata stream for the datastream.
 *
 * This looks for a function named 'fedora_importer_<METADATA-STREAM>' and 
 * applies it to the datastream.
 *
 * @param Omeka_Record $datastream The datastream to import metadata for.
 *
 * @return object This returns whatever the importer function returns.
 */
function fedora_connector_import_metadata($datastream)
{
    // XXX -> libraries/FedoraConnector/Importers.php
    $imps = new FedoraConnector_Importers();
    $imps->import($datastream);
}

/**
 * This walks a DOM node and returns the data from all the text nodes under it.
 *
 * @param DomNode $node The DOM node to get the text content for.
 *
 * @return string The text content for the DOM node passed in.
 */
function getNodeText($node)
{
    $text = '';

    switch ($node->nodeType) {
    case XML_DOCUMENT_NODE:
    case XML_ELEMENT_NODE:
        foreach ($node->childNodes as $child) {
            $text .= getNodeText($child);
        }
        break;

    case XML_TEXT_NODE:
    case XML_CDATA_SECTION_NODE:
        $text .= $node->nodeValue;
        break;

    case XML_ATTRIBUTE_NODE:
    case XML_ENTITY_REF_NODE:
    case XML_ENTITY_NODE:
    case XML_PI_NODE:
    case XML_COMMENT_NODE:
        break;
        
    }

    return $text;
}

/**
 * This takes the URI for an XML file and an XPath query and returns the nodes 
 * in the file that match the query.
 *
 * @param string $uri   The URI for the XML file.
 * @param string $xpath The XPath expression to search for in the file.
 *
 * @return DOMNodeList The nodes in the file that match the XPath query.
 */
function getQueryNodes($uri, $xpath)
{
    $xml = new DomDocument();
    $xml->load($uri);
    $query = new DOMXPath($xml);
    return $query->query($xpath);
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
