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
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
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
        $this->setPosition($rawPrediction);
    }
}
