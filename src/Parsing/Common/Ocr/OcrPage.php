<?php

namespace Mindee\Parsing\Common\Ocr;

use Mindee\Geometry\MinMaxUtils;
use Mindee\Geometry\PolygonUtils;

class OcrPage
{
    private array $allWords;
    private array $lines;

    private static function areWordsOnSameLine(OcrWord $current_word, OcrWord $next_word): bool
    {
        $current_in_next = PolygonUtils::is_point_in_polygon_y($current_word->polygon->getCentroid(), $next_word->polygon);
        $next_in_current = PolygonUtils::is_point_in_polygon_y($next_word->polygon->getCentroid(), $current_word->polygon);
        return $current_in_next || $next_in_current;
    }

    private static function getMinMaxY(OcrWord $word_1, OcrWord $word_2): int
    {
        $word_1_y = MinMaxUtils::get_min_max_y($word_1->polygon->getCoordinates())->getMin();
        $word_2_y = MinMaxUtils::get_min_max_y($word_2->polygon->getCoordinates())->getMin();
        if ($word_1_y == $word_2_y) {
            return 0;
        }
        return $word_1_y < $word_2_y ? -1 : 1;

    }

    private function toLines(): array
    {
        $current = null;
        $indexes = [];
        $lines = [];
        foreach ($this->allWords as $_) {
            $line = new OcrLine();
            for ($idx = 0; $idx < count($this->allWords); $idx++) {
                $word = $this->allWords[$idx];
                if (!in_array($idx, $indexes)) {
                    if ($current == null) {
                        $current = $word;
                        $indexes[] = $idx;
                        $line = new OcrLine();
                        $line->add($word);
                    } else {
                        if ($this->areWordsOnSameLine($current, $word)) {
                            $line->add($word);
                            $indexes[] = $idx;
                        }
                    }
                }
            }
            $current = null;
            if ($line->count()) {
                $line->sortOnX();
                $lines[] = $line;
            }
        }
        return $lines;
    }

    public function getAllLines(): array
    {
        if (!$this->lines) {
            $this->lines = $this->toLines();
        }
        return $this->lines;
    }

    public function getAllWords(): array
    {
        return $this->allWords;
    }

    function __construct($raw_prediction)
    {
        $this->allWords = [];
        foreach ($raw_prediction['all_words'] as $word_prediction) {
            $this->allWords[] = new OcrWord($word_prediction);
        }
        usort($this->allWords, "self::getMinMaxY");

    }

    public function __toString(): string
    {
        $lines_str = [];
        foreach ($this->lines as $line) {
            $lines_str[] = strval($line);
        }
        return implode("\n", $lines_str) . "\n";
    }
}
