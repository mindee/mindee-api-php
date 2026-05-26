<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Extras;

use Stringable;

/**
 * Full Text OCR result.
 */
class FullTextOCRExtra implements Stringable
{
    /**
     * @var string|null Text content of the full text ocr reading.
     */
    public ?string $content;


    /**
     * @var string|null Language of the ocr reading.
     */
    public ?string $language;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        if (isset($rawPrediction['content'])) {
            $this->content = $rawPrediction['content'];
        }
        if (isset($rawPrediction['language'])) {
            $this->language = $rawPrediction['language'];
        }
    }

    /**
     */
    public function __toString(): string
    {
        return $this->content ?? '';
    }
}
