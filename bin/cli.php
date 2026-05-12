<?php

declare(strict_types=1);

namespace Mindee\CLI;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/MindeeCLIDocuments.php';
require __DIR__ . '/MindeeCLICommand.php';

use Symfony\Component\Console\Application;
use Exception;

$cli = new Application();
$mindeeCommand = new MindeeCLICommand(MindeeCLIDocuments::getSpecs());
$cli->add($mindeeCommand);
try {
    $cli->add($mindeeCommand);
    $cli->setDefaultCommand($mindeeCommand->getName(), true);
    $cli->run();
} catch (Exception $e) {
    error_log("Could not start the Mindee CLI, an exception was raised:");
    error_log($e->getMessage());
}
