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
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, 'value');
        if (array_key_exists('value', $rawPrediction) && is_numeric($rawPrediction['value'])) {
            $this->value = number_format(floatval($rawPrediction['value']), 2, ".", "");
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
