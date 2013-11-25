<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


if (!defined('FEDORA__PLUGIN_VERSION')) {
    define('FEDORA__PLUGIN_VERSION', get_plugin_ini('FedoraConnector', 'version'));
}

if (!defined('FEDORA_PLUGIN_DIR')) {
    define('FEDORA_PLUGIN_DIR', dirname(__FILE__));
}

require_once FEDORA_PLUGIN_DIR . '/FedoraConnectorPlugin.php';
require_once FEDORA_PLUGIN_DIR . '/helpers/FedoraConnectorFunctions.php';
require_once FEDORA_PLUGIN_DIR . '/helpers/FedoraGateway.php';
require_once FEDORA_PLUGIN_DIR . '/libraries/FedoraConnector/Import.php';
require_once FEDORA_PLUGIN_DIR . '/libraries/FedoraConnector/Render.php';
require_once FEDORA_PLUGIN_DIR . '/libraries/FedoraConnector/Plugins.php';
require_once FEDORA_PLUGIN_DIR . '/libraries/FedoraConnector/AbstractImporter.php';
require_once FEDORA_PLUGIN_DIR . '/libraries/FedoraConnector/AbstractRenderer.php';
require_once FEDORA_PLUGIN_DIR . '/forms/ObjectForm.php';
require_once FEDORA_PLUGIN_DIR . '/forms/ServerForm.php';
require_once FEDORA_PLUGIN_DIR . '/forms/Validate/isUrl.php';

Zend_Registry::set('gateway', new FedoraGateway);

$fedora = new FedoraConnectorPlugin();
$fedora->setUp();
