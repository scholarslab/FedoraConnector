<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Helper methods.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


/**
 * Return nodes by file and xpath query.
 *
 * @param string $uri The uri of the document.
 * @param string $xpath The XPath query.
 *
 * @return object The matching nodes.
 */
function __fedoraNodes($uri, $xpath)
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


function fedora_connector_display_object($item = null, $params = array()) {
  $item = $item ? $item : get_current_item();

  if ($item && $object = get_db()->getTable('FedoraConnectorObject')->findByItem($item)) {
    $renderer = new FedoraConnector_Render();
    return $renderer->display($object, $params);
  }

  return false;
}
