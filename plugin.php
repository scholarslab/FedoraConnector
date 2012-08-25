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
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


// {{{ constants
if (!defined('FEDORA_CONNECTOR_PLUGIN_VERSION')) {
    define('FEDORA_CONNECTOR_PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
}

if (!defined('FEDORA_CONNECTOR_PLUGIN_DIR')) {
    define('FEDORA_CONNECTOR_PLUGIN_DIR', dirname(__FILE__));
}
// }}}

// {{{ requires
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/FedoraConnectorPlugin.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/helpers/FedoraConnectorFunctions.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/libraries/FedoraConnector/Import.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/libraries/FedoraConnector/Render.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/libraries/FedoraConnector/Plugins.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/forms/ObjectForm.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/forms/ServerForm.php';
require_once FEDORA_CONNECTOR_PLUGIN_DIR . '/forms/Validate/isUrl.php';
// }}}

new FedoraConnectorPlugin;
