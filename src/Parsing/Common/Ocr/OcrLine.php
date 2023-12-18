<?php

namespace Mindee\Parsing\Common\Ocr;

/**
 * A list of words which are on the same line.
 */
class OcrLine
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
        usort($this->words, "Mindee\\Parsing\\Common\\Ocr\\OcrPage::getMinMaxX");
    }

    /**
     * Appends a word to the line.
     *
     * @param \Mindee\Parsing\Common\Ocr\OcrWord $word Word to add.
     * @return void
     */
    public function add(OcrWord $word)
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
