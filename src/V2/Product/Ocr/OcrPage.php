<?php

namespace Mindee\V2\Product\Ocr;

/**
 * OCR result for a single page.
 */
class OcrPage
{
    /**
     * @var OcrWord[] OCR result for a single page.
     */
    public array $words;

    /**
     * @var string Full text content extracted from the document page.
     */
    public string $content;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->words = array_map(fn ($word) => new OcrWord($word), $rawResponse['words']);
        $this->content = $rawResponse['content'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $ocrWords = "\n";

        if (!empty($this->words)) {
            $ocrWords .= implode("\n\n", $this->words);
        }

        return "OCR Words\n---------$ocrWords\n\n:Content: $this->content";
    }
}
