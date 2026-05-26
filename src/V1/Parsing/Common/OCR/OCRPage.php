<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\OCR;

use function count;
use function in_array;

/**
 * OCR extraction for a single page.
 */
class OCRPage
{
    /**
     * @var array<OCRWord> List of all words.
     */
    private array $allWords;
    /**
     * @var array<OCRLine> List of lines.
     */
    private array $lines;

    /**
     * Checks whether the words are on the same line.
     *
     * @param OCRWord $currentWord Reference word to compare.
     * @param OCRWord $nextWord Next word to compare.
     * @return boolean
     */
    private static function areWordsOnSameLine(OCRWord $currentWord, OCRWord $nextWord): bool
    {
        $currentInNext = $nextWord->polygon->isPointInY($currentWord->polygon->getCentroid());
        $nextInCurrent = $currentWord->polygon->isPointInY($nextWord->polygon->getCentroid());
        return $currentInNext || $nextInCurrent;
    }

    /**
     * Compares word positions on the X axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param OCRWord $word1 First word.
     * @param OCRWord $word2 Second word.
     * @return integer
     */
    public static function getMinMaxX(OCRWord $word1, OCRWord $word2): int
    {
        $word1X = $word1->polygon->getMinMaxX()->getMin();
        $word2X = $word2->polygon->getMinMaxX()->getMin();
        if ($word1X === $word2X) {
            return 0;
        }
        return $word1X < $word2X ? -1 : 1;
    }

    /**
     * Compares word positions on the Y axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param OCRWord $word1 First word.
     * @param OCRWord $word2 Second word.
     * @return integer
     */
    public static function getMinMaxY(OCRWord $word1, OCRWord $word2): int
    {
        $word1Y = $word1->polygon->getMinMaxY()->getMin();
        $word2Y = $word2->polygon->getMinMaxY()->getMin();
        if ($word1Y === $word2Y) {
            return 0;
        }
        return $word1Y < $word2Y ? -1 : 1;
    }

    /**
     * Puts all words on the page into an array of lines.
     * @return array<OCRLine>
     */
    private function toLines(): array
    {
        $current = null;
        $indexes = [];
        $lines = [];
        foreach ($this->allWords as $_) {
            $line = new OCRLine();
            for ($idx = 0; $idx < count($this->allWords); $idx++) {
                $word = $this->allWords[$idx];
                if (!in_array($idx, $indexes, true)) {
                    if (null === $current) {
                        $current = $word;
                        $indexes[] = $idx;
                        $line = new OCRLine();
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
     * @return array<OCRLine>
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
     * @return array<OCRWord>
     */
    public function getAllWords(): array
    {
        return $this->allWords;
    }

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->allWords = [];
        foreach ($rawPrediction['all_words'] as $wordPrediction) {
            $this->allWords[] = new OCRWord($wordPrediction);
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
            $linesStr[] = (string) $line;
        }
        return implode("\n", $linesStr) . "\n";
    }
}
