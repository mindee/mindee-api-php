<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use function array_key_exists;

/**
 * A field containing a text value.
 * @extends BaseField<string>
 */
class StringField extends BaseField
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The value as it appears on the document.
     */
    public ?string $rawValue;


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
        $this->setPosition($rawPrediction);
        $this->rawValue = array_key_exists('raw_value', $rawPrediction) ? $rawPrediction['raw_value'] : null;
    }
}
