<?php

define('FEDORA_DIR', dirname(dirname(__FILE__)));
define('OMEKA_DIR', dirname(dirname(FEDORA_DIR)));
define('FEDORA_TEST_DIR', FEDORA_DIR.'/tests');

require_once OMEKA_DIR.'/application/tests/bootstrap.php';
require_once 'FedoraConnector_Test_AppTestCase.php';
