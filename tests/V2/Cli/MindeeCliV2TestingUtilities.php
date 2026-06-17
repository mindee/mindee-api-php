<?php

declare(strict_types=1);

namespace V2\Cli;

class MindeeCliV2TestingUtilities
{
    /**
     * Executes a CLI invocation against `bin/cli.php`.
     *
     * @param array<int, string> $args CLI arguments (each entry is shell-escaped before execution).
     * @param array<string, string|false> $envOverrides Environment variables to set/unset for the call.
     *                                                  Use `false` to unset a variable.
     * @return array{output: array<int, string>, code: int} Output lines and exit code.
     */
    public static function executeTest(array $args, array $envOverrides = []): array
    {
        $resCode = 0;
        $output = [];

        $envPrefix = '';
        foreach ($envOverrides as $key => $value) {
            if ($value === false) {
                $envPrefix .= 'unset ' . escapeshellarg($key) . '; ';
            } else {
                $envPrefix .= escapeshellarg($key) . '=' . escapeshellarg((string) $value) . ' ';
            }
        }

        $escaped = array_map(escapeshellarg(...), $args);
        $cmd = $envPrefix . 'php ./bin/cli.php ' . implode(' ', $escaped) . ' 2>&1';
        exec($cmd, $output, $resCode);

        return ['output' => $output, 'code' => $resCode];
    }
}
