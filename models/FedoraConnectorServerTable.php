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
 * Table class for servers.
 */
class FedoraConnectorServerTable extends Omeka_Db_Table
{

    /**
     * Returns servers for the main listing.
     *
     * @param string $order The constructed SQL order clause.
     *
     * @return object The collections.
     */
    public function getServers($page = null, $order = null)
    {

        $select = $this->getSelect();
        if (isset($page)) {
            $select->limitPage($page, get_option('per_page_admin'));
        }
        if (isset($order)) {
            $select->order($order);
        }

        return $this->fetchObjects($select);

    }

    /**
     * Delete server by id.
     *
     * @param string $id The id of the server to delete.
     *
     * @return boolean True if delete succeeds.
     */
    public function deleteServer($id)
    {

        $server = $this->find($id);
        return $server->delete() ? true : false;

    }

    /**
     * Save server information.
     *
     * @param array $data The field data posted from the form.
     *
     * @return boolean True if save succeeds.
     */
    public function saveServer($data)
    {

        $server = $this->find($data['id']);

        $server->name = $data['name'];
        $server->url = $data['url'];

        if ($data['is_default'] == 1 && $server->is_default == 0) {
            $old_default = $this->findBySql('is_default = 1');
            $old_default->is_default = 0;
            $old_default->save();
            $server->is_default = 1;
        }

        return $server->save() ? true : false;

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
