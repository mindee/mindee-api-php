<?php

namespace Mindee\Parsing\Common\Extras;

use function PHPUnit\Framework\isEmpty;

/**
 * Full Text OCR result.
 */
class FullTextOcrExtra
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
     * @param array $rawPrediction Raw HTTP response.
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
     * @return string
     */
    public function __toString()
    {
        return $this->content ?? '';
    }
}
