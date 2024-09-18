<?php

namespace Product;

class TestingUtilities
{
    public static function getVersion(string $rstStr)
    {
        $versionLineStartPos = mb_strpos($rstStr, ":Product: ", 0, "UTF-8");
        $versionEndPos = mb_strpos($rstStr, "\n", $versionLineStartPos, "UTF-8");

        $substring = mb_substr($rstStr, $versionLineStartPos, $versionEndPos - $versionLineStartPos, "UTF-8");
        $versionStartPos = mb_strrpos($substring, " v", 0, "UTF-8");

        return mb_substr($substring, $versionStartPos + 2, null, "UTF-8");
    }


    public static function getId($rstStr)
    {
        // Replaces the string of a created object to avoid errors during tests.

        $idEndPos = mb_strpos($rstStr, "\n:Filename:", 0, "UTF-8");
        $idStartPos = mb_strpos($rstStr, ":Mindee ID: ", 0, "UTF-8");

        return mb_substr($rstStr, $idStartPos + 12, $idEndPos - ($idStartPos + 12), "UTF-8");
    }

}
