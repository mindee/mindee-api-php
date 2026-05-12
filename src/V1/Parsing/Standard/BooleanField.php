<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * A field containing a boolean value.
 */
class BooleanField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var boolean|null Value as string.
     */
    public $value;


    /**
     * @param array $rawPrediction Raw prediction array.
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
        $this->setPosition($rawPrediction);
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::formatForDisplay($this->value);
    }
}
