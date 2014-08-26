<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Generate markup for an item's Fedora object.
 *
 * @param Item $item The item.
 * @param array $params Options for the renderer.
 * @return string|null The Fedora object markup.
 */
function fc_displayObject($item=null, $params=array()) {

    // Get the item and the objects table.
    $item = $item ? $item : get_current_record('item');
    $objectsTable = get_db()->getTable('FedoraConnectorObject');

    // Render the Fedora object.
    if ($item && $object = $objectsTable->findByItem($item)) {
        $renderer = new FedoraConnector_Render();
        return $renderer->display($object, $params);
    }

}

/**
 * Tests whether an item contains Fedora streams.
 *
 * @param Item $item The item.
 * @return bool Is the item a Fedora stream?
 * @author Eric Rochester <erochest@virginia.edu>
 **/
function fc_isFedoraStream($item=null)
{
    $item     = $item ? $item : get_current_record('item');
    $objects  = get_db()->getTable('FedoraConnectorObject');
    $isStream = $item && $objects->findByItem($item);

    return $isStream;
}
