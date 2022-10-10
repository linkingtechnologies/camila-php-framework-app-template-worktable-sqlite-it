#!/usr/bin/php
<?php

require '../../vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');

class CamilaAppCli extends CLI
{

    protected function setup(Options $options)
    {
		$options->registerCommand('init-app', 'Create new App');
        $options->registerArgument('lang', 'App language', true, 'init-app');
    }

    protected function main(Options $options)
    {
        switch ($options->getCmd()) {
            case 'init-app':
				$this->initApp($options);
                break;
            default:
                $this->error('No known command was called, we show the default help instead:');
                echo $options->help();
                exit;
        }
    }
	
	protected function initApp(Options $options) {
		$lang = $options->getArgs()[0];
		$camilaApp = new CamilaApp();
		$db = NewADOConnection(CAMILA_DB_DSN);
		$camilaApp->db = $db;
		$camilaApp->lang = $lang;
		$camilaApp->resetTables(CAMILA_TABLES_DIR);	
	}
}

$cli = new CamilaAppCli();
$cli->run();