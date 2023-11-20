<?php

namespace Mindee\Parsing\Standard;

class AmountField extends BaseField
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    public ?float $value;

    public function __construct(
        array $raw_prediction,
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, 'value');
        if (array_key_exists('value', $raw_prediction) && is_float($raw_prediction['value'])) {
            $this->value = round($raw_prediction['value'], 3);
        } else {
            $this->value = null;
            $this->confidence = 0.0;
        }
    }

    public function __toString(): string
    {
        return strval($this->value);
    }
}
