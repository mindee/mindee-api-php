<?php

namespace Mindee\Parsing\Standard;

/**
 * A field containing a text value.
 */
class StringField extends BaseField
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The value.
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
        $this->setPosition($raw_prediction);
    }
}
