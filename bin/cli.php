<?php

declare(strict_types=1);

namespace Mindee\Cli;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/version.php';
require __DIR__ . '/MindeeCliDocuments.php';
require __DIR__ . '/MindeeCliCommand.php';
require __DIR__ . '/V2/BaseInferenceCommand.php';
require __DIR__ . '/V2/ClassificationCommand.php';
require __DIR__ . '/V2/CropCommand.php';
require __DIR__ . '/V2/ExtractionCommand.php';
require __DIR__ . '/V2/OcrCommand.php';
require __DIR__ . '/V2/SplitCommand.php';
require __DIR__ . '/V2/SearchModelsCommand.php';

use Exception;
use Mindee\Cli\V2\ClassificationCommand;
use Mindee\Cli\V2\CropCommand;
use Mindee\Cli\V2\ExtractionCommand;
use Mindee\Cli\V2\OcrCommand;
use Mindee\Cli\V2\SearchModelsCommand;
use Mindee\Cli\V2\SplitCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

use function defined;
use function getenv;
use function in_array;

/**
 * Checks whether the CLI should display explicit error_log() output.
 *
 * @param array<int, string> $argv CLI arguments.
 * @return boolean True when the error log option or verbosity is present.
 */
function mindeeCliShouldDisplayErrorLog(array $argv): bool
{
    $shellVerbosity = getenv('SHELL_VERBOSITY');
    if ($shellVerbosity !== false && (int) $shellVerbosity > 0) {
        return true;
    }

    foreach ($argv as $arg) {
        if ($arg === '--error-log' || $arg === '--verbose' || str_starts_with($arg, '-v')) {
            return true;
        }
    }

    return false;
}

/**
 * Redirects explicit error_log() output away from stderr unless requested.
 *
 * @param boolean $displayErrorLog Whether error_log() output should be displayed.
 */
function mindeeCliConfigureErrorLog(bool $displayErrorLog): void
{
    if ($displayErrorLog) {
        return;
    }

    ini_set('error_log', stripos(PHP_OS, 'WIN') === 0 ? 'NUL' : '/dev/null');
}

/**
 * Checks whether an argv token is a CLI-level option that does not consume a value.
 *
 * @param string $arg CLI argument.
 * @return boolean True when the argument can be skipped before command dispatch.
 */
function mindeeCliIsGlobalOptionWithoutValue(string $arg): bool
{
    return $arg === '--error-log' || $arg === '--verbose' || str_starts_with($arg, '-v');
}

/**
 * Rewrites argv for V1 backward compatibility.
 *
 * If the first non-global-option argument is not a registered top-level command
 * (V2 inference commands, `search-models`, `v1`, or a Symfony built-in like
 * `help`/`list`/`completion`) and is not an option, it is treated as a V1
 * product name and `v1` is inserted before it. This preserves the legacy
 * `mindee <v1product> ...` invocation shape while letting V2 commands run
 * unmodified.
 *
 * @param array<int, string> $argv Original argv array.
 * @param array<int, string> $knownTopLevelCommands Top-level commands not to rewrite.
 * @return array<int, string> Possibly rewritten argv array.
 */
function mindeeRewriteArgvForV1Compat(array $argv, array $knownTopLevelCommands): array
{
    $commandIndex = 1;
    while (isset($argv[$commandIndex]) && mindeeCliIsGlobalOptionWithoutValue($argv[$commandIndex])) {
        $commandIndex++;
    }

    if (!isset($argv[$commandIndex])) {
        return $argv;
    }
    $first = $argv[$commandIndex];
    if ($first === '' || $first[0] === '-') {
        return $argv;
    }
    if (in_array($first, $knownTopLevelCommands, true)) {
        return $argv;
    }
    array_splice($argv, $commandIndex, 0, ['v1']);
    return $argv;
}

$displayErrorLog = mindeeCliShouldDisplayErrorLog($_SERVER['argv']);
mindeeCliConfigureErrorLog($displayErrorLog);

$cli = new Application('mindee', defined('Mindee\\VERSION') ? \Mindee\VERSION : 'unknown');
$cli->getDefinition()->addOption(new InputOption(
    'error-log',
    null,
    InputOption::VALUE_NONE,
    'Display PHP error_log() output. Also enabled by verbose output.'
));

$v1Specs = MindeeCliDocuments::getSpecs();
$v1Command = new MindeeCliCommand($v1Specs);
$cli->add($v1Command);

$v2InferenceCommands = [
    new ClassificationCommand(),
    new CropCommand(),
    new ExtractionCommand(),
    new OcrCommand(),
    new SplitCommand(),
];
foreach ($v2InferenceCommands as $command) {
    $cli->add($command);
}
$cli->add(new SearchModelsCommand());

$knownTopLevelCommands = ['v1', 'search-models', 'list', 'help', 'completion'];
foreach ($v2InferenceCommands as $command) {
    $knownTopLevelCommands[] = $command->getName();
}

$argv = mindeeRewriteArgvForV1Compat($_SERVER['argv'], $knownTopLevelCommands);

try {
    $cli->run(new ArgvInput($argv));
} catch (Exception $e) {
    if ($displayErrorLog) {
        error_log('Could not start the Mindee CLI, an exception was raised:');
        error_log($e->getMessage());
    }
}
