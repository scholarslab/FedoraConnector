<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


define('FEDORA_DIR', dirname(dirname(dirname(__FILE__))));
define('FEDORA_TEST_DIR', FEDORA_DIR.'/tests/phpunit');
define('OMEKA_DIR', dirname(dirname(FEDORA_DIR)));

// Bootstrap Omeka.
require_once OMEKA_DIR.'/application/tests/bootstrap.php';

// Laod the base test case.
require_once FEDORA_TEST_DIR.'/cases/FedoraConnector_Case_Default.php';
