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
require('../../camila/plugins.class.inc.php');

class CamilaAppCli extends CLI
{

    protected function setup(Options $options)
    {
		$options->registerCommand('init-app', 'Create new App');
        $options->registerArgument('lang', 'App language', true, 'init-app');
		
		$options->registerCommand('install-plugin', 'Install plugin');
        $options->registerArgument('name', 'Plugin name', true, 'install-plugin');
		$options->registerArgument('lang', 'Plugin language', true, 'install-plugin');
    }

    protected function main(Options $options)
    {
        switch ($options->getCmd()) {
            case 'init-app':
				$this->initApp($options);
                break;
			case 'install-plugin':
				$this->installPlugin($options);
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
	
	protected function installPlugin(Options $options) {
		$name = $options->getArgs()[0];
		$lang = $options->getArgs()[1];
		if (is_dir('plugins/'.$name)) {
			$this->error('Plugin already in use!');
		} else {
			$zipFile = bin2hex(random_bytes(10)).'.zip';
			file_put_contents('plugins/'.$zipFile, file_get_contents('https://github.com/linkingtechnologies/camila-php-framework-app-plugin-'.$name.'/archive/refs/heads/main.zip'));
			$zip = new ZipArchive;
			if ($zip->open('plugins/'.$zipFile) === TRUE) {
				$zip->extractTo('plugins/');
				$zip->close();
				rename('plugins/camila-php-framework-app-plugin-'.$name.'-main', 'plugins/'.$name);
				unlink('plugins/'.$zipFile);
				global $_CAMILA;
				CamilaPlugins::install($_CAMILA['db'], $lang, $name);
				$this->success('Plugin ' . $options->getArgs()[0] . ' installed!');
			} else {
				$this->error('Error extracting template zip file');
			}
		}
	}
}

$cli = new CamilaAppCli();
$cli->run();