<?php

namespace Mindee\Parsing\Common\Ocr;

use Mindee\Geometry\MinMaxUtils;
use Mindee\Geometry\PolygonUtils;

/**
 * OCR extraction for a single page.
 */
class OcrPage
{
    /**
     * @var array List of all words.
     */
    private array $allWords;
    /**
     * @var array List of lines.
     */
    private array $lines;

    /**
     * Checks whether the words are on the same line.
     *
     * @param \Mindee\Parsing\Common\Ocr\OcrWord $current_word Reference word to compare.
     * @param \Mindee\Parsing\Common\Ocr\OcrWord $next_word    Next word to compare.
     * @return boolean
     */
    private static function areWordsOnSameLine(OcrWord $current_word, OcrWord $next_word): bool
    {
        $current_in_next = PolygonUtils::isPointInPolygonY($current_word->polygon->getCentroid(), $next_word->polygon);
        $next_in_current = PolygonUtils::isPointInPolygonY($next_word->polygon->getCentroid(), $current_word->polygon);
        return $current_in_next || $next_in_current;
    }

    /**
     * Compares word positions on the Y axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param \Mindee\Parsing\Common\Ocr\OcrWord $word_1 First word.
     * @param \Mindee\Parsing\Common\Ocr\OcrWord $word_2 Second word.
     * @return integer
     */
    private static function getMinMaxY(OcrWord $word_1, OcrWord $word_2): int
    {
        $word_1_y = MinMaxUtils::getMinMaxY($word_1->polygon->getCoordinates())->getMin();
        $word_2_y = MinMaxUtils::getMinMaxY($word_2->polygon->getCoordinates())->getMin();
        if ($word_1_y == $word_2_y) {
            return 0;
        }
        return $word_1_y < $word_2_y ? -1 : 1;
    }

    /**
     * Puts all words on the page into an array of lines.
     *
     * @return array
     */
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

    /**
     * Retrieves all lines on the page.
     *
     * @return array
     */
    public function getAllLines(): array
    {
        if (!$this->lines) {
            $this->lines = $this->toLines();
        }
        return $this->lines;
    }

    /**
     * Retrieves all words on the page.
     *
     * @return array
     */
    public function getAllWords(): array
    {
        return $this->allWords;
    }

    /**
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(array $raw_prediction)
    {
        $this->allWords = [];
        foreach ($raw_prediction['all_words'] as $word_prediction) {
            $this->allWords[] = new OcrWord($word_prediction);
        }
        usort($this->allWords, "self::getMinMaxY");
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $lines_str = [];
        foreach ($this->getAllLines() as $line) {
            $lines_str[] = strval($line);
        }
        return implode("\n", $lines_str) . "\n";
    }
}
