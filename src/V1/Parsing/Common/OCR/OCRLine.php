<?php

namespace Mindee\V1\Parsing\Common\OCR;

/**
 * A list of words which are on the same line.
 */
class OCRLine
{
    /**
     * @var array Words in the line.
     */
    private array $words;

    /**
     * @param array $words Words to insert in the line.
     */
    public function __construct(array $words = [])
    {
        $this->words = $words;
    }

    /**
     * Sort the words on the line from left to right.
     *
     * @return void
     */
    public function sortOnX()
    {
        usort($this->words, "Mindee\\V1\\Parsing\\Common\\OCR\\OCRPage::getMinMaxX");
    }

    /**
     * Appends a word to the line.
     *
     * @param OCRWord $word Word to add.
     * @return void
     */
    public function add(OCRWord $word)
    {
        $this->words[] = $word;
    }

    /**
     * Returns the count of words in the line.
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->words);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $wordsStr = [];
        foreach ($this->words as $word) {
            $wordsStr[] = $word->text;
        }
        return implode(" ", $wordsStr);
    }
}
