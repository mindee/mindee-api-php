<?php

namespace Product;

class RegressionUtilities
{
    public static function getVersion(string $rstStr) {
        $versionLineStartPos = strpos($rstStr, ":Product: ");
        $versionEndPos = strpos($rstStr, "\n", $versionLineStartPos);

        $substring = substr($rstStr, $versionLineStartPos, $versionEndPos - $versionLineStartPos);
        $versionStartPos = strrpos($substring, " v");

        return substr($substring, $versionStartPos + 2);
    }


    public static function getId($rstStr) {
        // Replaces the string of a created object to avoid errors during tests.

        $idEndPos = strpos($rstStr, "\n:Filename:");
        $idStartPos = strpos($rstStr, ":Mindee ID: ");

        return substr($rstStr, $idStartPos + 12, $idEndPos - ($idStartPos + 12));
    }

}
