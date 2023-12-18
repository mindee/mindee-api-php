<?php

namespace Mindee\Parsing\Standard;

/**
 * A company registration item.
 */
class CompanyRegistrationField extends BaseField
{
    use FieldPositionMixin;

    /**
     * @var string|mixed The type of registration.
     */
    public string $type;


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
        $this->type = $rawPrediction['type'];
        $this->setPosition($rawPrediction);
    }


    /**
     * @return string String representation.
     */
    public function print(): string
    {
        return isset($this->value) ? "$this->type: $this->value" : '';
    }
}
