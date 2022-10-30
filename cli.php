#!/usr/bin/php
<?php

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

require __DIR__. './camila/cli/Exception.php';
require __DIR__. './camila/cli/TableFormatter.php';
require __DIR__. './camila/cli/Options.php';
require __DIR__. './camila/cli/Base.php';
require __DIR__. './camila/cli/Colors.php';
require __DIR__. './camila/cli/CLI.php';

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