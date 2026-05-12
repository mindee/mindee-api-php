<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use function array_key_exists;

/**
 * A field containing an amount value.
 */
class AmountField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null The amount value as a float.
     */
    public $value;


    /**
     * @param array $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     * @param boolean $reconstructed Whether the field was reconstructed.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, 'value');
        if (array_key_exists('value', $rawPrediction) && is_numeric($rawPrediction['value'])) {
            $this->value = (float) ($rawPrediction['value']);
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
        return isset($this->value) ? number_format((float) $this->value, 2, ".", "") : '';
    }
}
