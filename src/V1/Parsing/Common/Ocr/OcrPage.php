<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Ocr;

use Stringable;

use function count;
use function in_array;

/**
 * Ocr extraction for a single page.
 */
class OcrPage implements Stringable
{
    /**
     * @var array<OcrWord> List of all words.
     */
    private array $allWords;
    /**
     * @var array<OcrLine> List of lines.
     */
    private array $lines;

    /**
     * Checks whether the words are on the same line.
     *
     * @param OcrWord $currentWord Reference word to compare.
     * @param OcrWord $nextWord Next word to compare.
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
        return $word1X <=> $word2X;
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
        return $word1Y <=> $word2Y;
    }

    /**
     * Puts all words on the page into an array of lines.
     * @return array<OcrLine>
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
                if (!in_array($idx, $indexes, true)) {
                    if (null === $current) {
                        $current = $word;
                        $indexes[] = $idx;
                        $line = new OcrLine();
                        $line->add($word);
                    } else {
                        if (self::areWordsOnSameLine($current, $word)) {
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
     * @return array<OcrLine>
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
     * @return array<OcrWord>
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
            $this->allWords[] = new OcrWord($wordPrediction);
        }
        usort($this->allWords, self::getMinMaxY(...));
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
