<?php

namespace Mindee\Parsing\V2;

/**
 * Inference result options class.
 */
class InferenceResultOptions
{
    /**
     * @var RawText[] List of texts found per page.
     */
    public array $rawTexts;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->rawTexts = isset($serverResponse['raw_texts']) && is_array($serverResponse['raw_texts'])
            ? array_map(fn ($rawText) => new RawText($rawText), $serverResponse['raw_texts'])
            : [];
    }

    /**
     * @return string String representation.
     */
    public function toString(): string
    {
        if (empty($this->rawTexts)) {
            return '';
        }

        $parts = [];
        foreach ($this->rawTexts as $rawText) {
            $parts[] = $rawText->toString();
        }

        return implode("\n", $parts);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
