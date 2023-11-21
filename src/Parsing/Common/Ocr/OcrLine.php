<?php

namespace Mindee\Parsing\Common\Ocr;

class OcrLine
{
    private array $words;

    public function __construct(array $words = [])
    {
        $this->words = $words;
    }

    public function sortOnX()
    {
        usort($this->words, "Mindee\\Parsing\\Common\\Ocr\\OcrPage::getMinMaxY");
    }

    public function add(OcrWord $word)
    {
        $this->words[] = $word;
    }

    public function count(): int
    {
        return count($this->words);
    }

    public function __toString(): string
    {
        $words_str = [];
        foreach ($this->words as $word) {
            $words_str[] = $word->text;
        }
        return implode(" ", $words_str);
    }
}
