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

  try {

    $xml->load($uri);
    $query = new DOMXPath($xml);
    $result = $query->query($xpath);

  } catch (Exception $e) {

    $result = false;

  }

  return $result;

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
function fedorahelpers_getItems($page = null, $order = null, $search = null)
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
    $select->where("(SELECT text from `$db->ElementText` WHERE record_id = item.id AND element_id = 50 LIMIT 1) like '%" . $search . "%'");
  }

  return $itemTable->fetchObjects($select);

}

/**
 * Retrieves a single item with added columns with name, etc.
 *
 * TODO: wtf
 *
 * @param $id The id of the item.
 *
 * @return object $item The item.
 */
function fedorahelpers_getSingleItem($id)
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
  ))
  ->where('item.id = ' . $id);

  return $itemTable->fetchObject($select);

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

  ob_start();
  include FEDORA_CONNECTOR_PLUGIN_DIR . '/forms/item_import_form.php';
  return ob_get_clean();

}

/**
 * Build the form for the Edit Item menu.
 *
 * @param object $item The item being edited.
 *
 * @return string The form.
 */
function fedorahelpers_doItemFedoraImportSettings($item)
{

  $db = get_db();

  $elements = $db->getTable('Element')->findBySet('Dublin Core');
  $itemDefault = $db->getTable('FedoraConnectorImportSetting')
    ->getItemDefault($item, true);

  ob_start();
  include FEDORA_CONNECTOR_PLUGIN_DIR . '/forms/item_settings_form.php';
  return ob_get_clean();

}

/**
 * Truncate a string to a given length and add '...'.
 *
 * @param string $string The string to be truncated.
 * @param int $length The length to truncate to.
 *
 * @return string The truncated and formatted string.
 */
function fedorahelpers_previewString($string, $length)
{

  if (count($string) > $length) {
    return substr($string, 0, $length) . '...';
  }

  else {
    return $string;
  }

}

function fedorahelper_isFedoraStream($item)
{
  $db = get_db();
  $datastreams = $db->getTable('FedoraConnectorDatastream')
    ->findBySql('item_id = ?', array($item->id));

  $result = false;
  if(!empty($datastreams)) {
    $result = true;
  }

  return $result;
}

function fedorahelpers_getItemsShow($item)
{

  $html = '';

  $db = get_db();
  $datastreams = $db->getTable('FedoraConnectorDatastream')
    ->findBySql('item_id = ?', array($item->id));


  foreach ($datastreams as $datastream) {
    $renderer = new FedoraConnector_Render;
    $html .= $renderer->display($datastream);
  }

  return $html;

}
