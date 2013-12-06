<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

// Load the Omeka bootstrap.
require_once '../../../../application/tests/bootstrap.php';

// Assign directory globals.
define('FEDORA_DIR', PLUGIN_DIR.'/FedoraConnector');
define('FEDORA_TEST_DIR', FEDORA_DIR.'/tests/phpunit');

// Laod the base test case.
require_once FEDORA_TEST_DIR.'/cases/FedoraConnector_Case_Default.php';
