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
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages PDF.
     * @param boolean      $reconstructed  Whether the field was reconstructed.
     * @param string       $value_key      Key to use for the value.
     */
    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
    }
}
