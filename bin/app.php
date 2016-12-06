<?php

require_once __DIR__. '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Mev\ConsoleQueryTool\Command\QueryCommand;

$application = new Application();

$application->addCommands([
    new QueryCommand()
]);


$application->run();