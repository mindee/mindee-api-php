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

        $previousEnv = [];
        foreach ($envOverrides as $key => $value) {
            $previousEnv[$key] = getenv($key);
            if ($value === false) {
                putenv($key);
            } else {
                putenv($key . '=' . (string) $value);
            }
        }

        try {
            $escaped = array_map(escapeshellarg(...), $args);
            $cliPath = escapeshellarg(__DIR__ . '/../../../bin/cli.php');
            $cmd = PHP_BINARY . ' ' . $cliPath . ' ' . implode(' ', $escaped) . ' 2>&1';
            exec($cmd, $output, $resCode);
        } finally {
            foreach ($previousEnv as $key => $prev) {
                if ($prev === false) {
                    putenv($key);
                } else {
                    putenv($key . '=' . $prev);
                }
            }
        }

        return ['output' => $output, 'code' => $resCode];
    }
}
