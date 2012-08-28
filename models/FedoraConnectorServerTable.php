<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Table class for servers.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */

/**
 * Table class for servers.
 */
class FedoraConnectorServerTable extends Omeka_Db_Table
{

    /**
     * Create a new server.
     *
     * @param Omeka_Reocrd $server The server.
     * @param array $post The field data posted from the form.
     *
     * @return boolean True if insert succeeds.
     */
    public function updateServer($server, $post)
    {

        // Create server.
        $server->name = $post['name'];
        $server->url = $post['url'];

        // If there is a trailing slash on the URL, remove it.
        if (substr($server->url, -1) == '/') {
            $server->url = substr($server->url, 0, -1);
        }

        // Save.
        $server->save();
        return $server;

    }

}
