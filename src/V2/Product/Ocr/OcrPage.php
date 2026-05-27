<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Ocr;

use Stringable;

/**
 * Ocr result for a single page.
 */
class OcrPage implements Stringable
{
    /**
     * @var OcrWord[] Ocr result for a single page.
     */
    public array $words;

    /**
     * @var string Full text content extracted from the document page.
     */
    public string $content;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->words = array_map(static fn($word) => new OcrWord($word), $rawResponse['words']);
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

        return "Ocr Words\n---------$ocrWords\n\n:Content: $this->content";
    }
}
