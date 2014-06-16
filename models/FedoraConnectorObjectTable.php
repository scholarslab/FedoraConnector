<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


class FedoraConnectorObjectTable extends Omeka_Db_Table
{


    /**
     * Try to find a record for an item. If no record exists, return false.
     *
     * @param Omeka_record $item The item.
     * @return Omeka_record $datastream The datastream, if one exists.
     */
    public function findByItem($item)
    {

        $datastream = $this->fetchObject(
            $this->getSelect()->where('item_id=?', $item->id)
        );

        return $datastream ? $datastream : false;

    }


    /**
     * For a given item, try to find an existing datastream for the item. If
     * one exists, update it. Otherwise, create one.
     *
     * @param Omeka_record $item The item.
     * @param integer $serverId The server id.
     * @param string $pid The datastream pid.
     * @param array $dsids The datastream dsids.
     * @return Omeka_record $edition The service.
     */
    public function createOrUpdate($item, $serverId, $pid, $dsids)
    {

        // Try to get existing record.
        $record = $this->findByItem($item);

        // If no record exists, create a new one.
        if (!$record) { $record = new FedoraConnectorObject(); }

        // If data is empty, delete.
        if ($pid === '') { $record->delete(); }

        else {

            // Update and save.
            $record->item_id = $item->id;
            $record->server_id = $serverId;
            $record->pid = $pid;
            $record->dsids = implode(',', $dsids);
            $record->save();

        }

        return $record;

    }


}
