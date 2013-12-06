<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=80; */

/**
 * @package     omeka
 * @subpackage  fedora-connector
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


define('FEDORA_DIR', dirname(dirname(__FILE__)));
define('OMEKA_DIR', dirname(dirname(FEDORA_DIR)));
define('FEDORA_TEST_DIR', FEDORA_DIR.'/tests');

require_once OMEKA_DIR.'/application/tests/bootstrap.php';
require_once 'cases/FedoraConnector_Test_AppTestCase.php';
