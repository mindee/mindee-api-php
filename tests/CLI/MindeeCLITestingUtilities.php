<?php

namespace CLI;

use Mindee\Product;

class MindeeCLITestingUtilities
{
    public static function executeTest($args, $mute = false)
    {
        $resCode = 0;
        $output = "";
        if (!$mute) {
            exec("php ./bin/cli.php " . implode(" ", $args), $output, $resCode);
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
            ob_start();
            $nullDevice = (stripos(PHP_OS, 'WIN') === 0) ? 'NUL' : '/dev/null';
            exec("php ./bin/cli.php " . implode(" ", $args) . " > $nullDevice", $output, $resCode);
            ob_end_clean();
        }
        return ["output" => $output, "code" => $resCode];
    }
}
