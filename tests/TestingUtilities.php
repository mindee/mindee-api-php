<?php


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

    private static function lev($s, $t)
    {
        $m = strlen($s);
        $n = strlen($t);

        for ($i = 0; $i <= $m; $i++) $d[$i][0] = $i;
        for ($j = 0; $j <= $n; $j++) $d[0][$j] = $j;

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $c = ($s[$i - 1] == $t[$j - 1]) ? 0 : 1;
                $d[$i][$j] = min($d[$i - 1][$j] + 1, $d[$i][$j - 1] + 1, $d[$i - 1][$j - 1] + $c);
            }
        }

        return $d[$m][$n];
    }

    public static function levenshteinRatio(string $ref, string $target)
    {
        $lev = TestingUtilities::lev($ref, $target);
        $maxLen = max(strlen($ref), strlen($target));
        if ($maxLen === 0) {
            return 1.0;
        }

        return 1.0 - ($lev / $maxLen);
    }
}
