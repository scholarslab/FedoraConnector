<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Datastreams table.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

class FedoraConnectorDatastreamTable extends Omeka_Db_Table
{

    /**
     * Try to find a record for an item. If no record exists, return false.
     *
     * @param Omeka_record $item The item.
     *
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
     * For a given item, try to find an existing datastream record for the item.
     * If one exists, update it.  If a record does not already exist for the item,
     * create a new record.
     *
     * @param Omeka_record $item The item.
     * @param integer $serverId The server id.
     * @param string $pid The datastream pid.
     * @param array $dsids The datastream dsids.
     *
     * @return Omeka_record $edition The new or updated service.
     */
    public function createOrUpdate($item, $serverId, $pid, $dsids)
    {

        // Try to get existing record.
        $record = $this->findByItem($item);

        // If no record exists, create a new one.
        if (!$record) { $record = new FedoraConnectorDatastream(); }

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
