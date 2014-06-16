<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


if (!defined('FEDORA_DIR')) define('FEDORA_DIR', dirname(__FILE__));

// PLUGIN
require_once FEDORA_DIR.'/FedoraConnectorPlugin.php';

// HELPERS
require_once FEDORA_DIR.'/helpers/FedoraConnectorFunctions.php';
require_once FEDORA_DIR.'/helpers/FedoraGateway.php';

// LIBRARIES
require_once FEDORA_DIR.'/libraries/FedoraConnector/Import.php';
require_once FEDORA_DIR.'/libraries/FedoraConnector/Render.php';
require_once FEDORA_DIR.'/libraries/FedoraConnector/Plugins.php';
require_once FEDORA_DIR.'/libraries/FedoraConnector/AbstractImporter.php';
require_once FEDORA_DIR.'/libraries/FedoraConnector/AbstractRenderer.php';

// FORMS
require_once FEDORA_DIR.'/forms/ObjectForm.php';
require_once FEDORA_DIR.'/forms/ServerForm.php';
require_once FEDORA_DIR.'/forms/Validate/isUrl.php';

// Inject the live Fedora adapter.
Zend_Registry::set('gateway', new FedoraGateway);

$fedora = new FedoraConnectorPlugin();
$fedora->setUp();
