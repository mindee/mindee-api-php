<?php

namespace Mindee\Parsing\Common\Ocr;

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
     * @param OcrWord $currentWord Reference word to compare.
     * @param OcrWord $nextWord    Next word to compare.
     * @return boolean
     */
    private static function areWordsOnSameLine(OcrWord $currentWord, OcrWord $nextWord): bool
    {
        $currentInNext = $nextWord->polygon->isPointInY($currentWord->polygon->getCentroid());
        $nextInCurrent = $currentWord->polygon->isPointInY($nextWord->polygon->getCentroid());
        return $currentInNext || $nextInCurrent;
    }

    /**
     * Compares word positions on the X axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param OcrWord $word1 First word.
     * @param OcrWord $word2 Second word.
     * @return integer
     */
    public static function getMinMaxX(OcrWord $word1, OcrWord $word2): int
    {
        $word1X = $word1->polygon->getMinMaxX()->getMin();
        $word2X = $word2->polygon->getMinMaxX()->getMin();
        if ($word1X == $word2X) {
            return 0;
        }
        return $word1X < $word2X ? -1 : 1;
    }

    /**
     * Compares word positions on the Y axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param OcrWord $word1 First word.
     * @param OcrWord $word2 Second word.
     * @return integer
     */
    public static function getMinMaxY(OcrWord $word1, OcrWord $word2): int
    {
        $word1Y = $word1->polygon->getMinMaxY()->getMin();
        $word2Y = $word2->polygon->getMinMaxY()->getMin();
        if ($word1Y == $word2Y) {
            return 0;
        }
        return $word1Y < $word2Y ? -1 : 1;
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
        foreach ($this->allWords as $w) {
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
        if (!isset($this->lines)) {
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
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->allWords = [];
        foreach ($rawPrediction['all_words'] as $wordPrediction) {
            $this->allWords[] = new OcrWord($wordPrediction);
        }
        usort($this->allWords, "self::getMinMaxY");
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $linesStr = [];
        foreach ($this->getAllLines() as $line) {
            $linesStr[] = strval($line);
        }
        return implode("\n", $linesStr) . "\n";
    }
}
