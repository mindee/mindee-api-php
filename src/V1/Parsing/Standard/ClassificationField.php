<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

/**
 * Represents a classifier value.
 * @extends BaseField<string>
 */
class ClassificationField extends BaseField
{
    /**
     * @var float The confidence score.
     */
    public float $confidence;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     * @param boolean $reconstructed Whether the field was reconstructed.
     * @param string $valueKey Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'value'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);
    }
}
