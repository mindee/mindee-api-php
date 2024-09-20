<?php

/**
 * Testing utility class.
 */
class TestingUtilities
{
    /**
     * Retrieves the version in a string of a prediction's RST.
     *
     * @param string $rstStr The input string.
     * @return string The version present in the string.
     */
    public static function getVersion(string $rstStr): string
    {
        $versionLineStartPos = mb_strpos($rstStr, ":Product: ", 0, "UTF-8");
        $versionEndPos = mb_strpos($rstStr, "\n", $versionLineStartPos, "UTF-8");

        $substring = mb_substr($rstStr, $versionLineStartPos, $versionEndPos - $versionLineStartPos, "UTF-8");
        $versionStartPos = mb_strrpos($substring, " v", 0, "UTF-8");

        return mb_substr($substring, $versionStartPos + 2, null, "UTF-8");
    }


    /**
     * Retrieves the ID in a string of a prediction's RST.
     *
     * @param string $rstStr The input string.
     * @return string The ID present in the string.
     */
    public static function getId(string $rstStr): string
    {
        $idEndPos = mb_strpos($rstStr, "\n:Filename:", 0, "UTF-8");
        $idStartPos = mb_strpos($rstStr, ":Mindee ID: ", 0, "UTF-8");

        return mb_substr($rstStr, $idStartPos + 12, $idEndPos - ($idStartPos + 12), "UTF-8");
    }

    /**
     * Levenshtein distance function.
     * Taken from:
     * https://stackoverflow.com/questions/52201109/why-does-the-function-levenshtein-in-php-have-a-255-character-limit
     * Which in turn was taken from:
     * https://en.wikibooks.org/wiki/Algorithm_Implementation/Strings/Levenshtein_distance#PHP
     * @param string $referenceStr Base string.
     * @param string $targetStr    String to compare.
     * @return integer Levenshtein distance between the two strings.
     */
    private static function lev(string $referenceStr, string $targetStr): int
    {
        $refLength = strlen($referenceStr);
        $targetLength = strlen($targetStr);

        for ($i = 0; $i <= $refLength; $i++) {
            $distanceTable[$i][0] = $i;
        }
        for ($j = 0; $j <= $targetLength; $j++) {
            $distanceTable[0][$j] = $j;
        }

        for ($i = 1; $i <= $refLength; $i++) {
            for ($j = 1; $j <= $targetLength; $j++) {
                $c = ($referenceStr[$i - 1] == $targetStr[$j - 1]) ? 0 : 1;
                $distanceTable[$i][$j] = min(
                    $distanceTable[$i - 1][$j] + 1,
                    $distanceTable[$i][$j - 1] + 1,
                    $distanceTable[$i - 1][$j - 1] + $c
                );
            }
        }

        return $distanceTable[$refLength][$targetLength];
    }

    /**
     * Computes the levenshtein ratio between two strings.
     *
     * @param string $ref    Reference string.
     * @param string $target Target string.
     * @return float The levenshtein ratio.
     */
    public static function levenshteinRatio(string $ref, string $target): float
    {
        $lev = TestingUtilities::lev($ref, $target);
        $maxLen = max(strlen($ref), strlen($target));
        if ($maxLen === 0) {
            return 1.0;
        }

        return 1.0 - ($lev / $maxLen);
    }
}
