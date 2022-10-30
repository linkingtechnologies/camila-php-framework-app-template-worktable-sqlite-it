#!/usr/bin/php
<?php

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

require CAMILA_DIR. 'cli/Exception.php';
require CAMILA_DIR. 'cli/TableFormatter.php';
require CAMILA_DIR. 'cli/Options.php';
require CAMILA_DIR. 'cli/Base.php';
require CAMILA_DIR. 'cli/Colors.php';
require CAMILA_DIR. 'cli/CLI.php';

class CamilaAppCli extends CLI
{
	protected function setup(Options $options)
    {
		$this->registerDefaultCommands($options);
		$this->registerAppCommands($options);
    }

    protected function main(Options $options)
    {
		$this->handleAppCommands($options);
    }
}

$cli = new CamilaAppCli();
$cli->run();