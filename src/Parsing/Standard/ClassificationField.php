<?php

namespace Mindee\Parsing\Standard;

/**
 * Represents a classifier value.
 */
class ClassificationField extends BaseField
{
    use FieldConfidenceMixin;

    /**
     * @var string The value as a string
     */
    public $value;


    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages PDF.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the value.
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
