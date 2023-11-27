<?php

namespace Mindee\Parsing\Standard;

/**
 * A field containing an amount value.
 */
class AmountField extends BaseField
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The amount value as a float.
     */
    public $value;


    /**
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages PDF.
     * @param boolean      $reconstructed  Whether the field was reconstructed.
     */
    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, 'value');
        if (array_key_exists('value', $raw_prediction) && is_float($raw_prediction['value'])) {
            $this->value = round(floatval($raw_prediction['value']), 3);
        } else {
            $this->value = null;
            $this->confidence = 0.0;
        }
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return strval($this->value);
    }
}
