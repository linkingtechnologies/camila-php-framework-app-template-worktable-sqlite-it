<?php
require '../../vendor/autoload.php';
require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');
//require('../../camila/plugins.class.inc.php');

$cli = new CamilaAppCli();
$cli->run();