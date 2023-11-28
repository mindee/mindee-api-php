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
        $this->type = $raw_prediction['type'];
        $this->setPosition($raw_prediction);
    }


    /**
     * @return string String representation.
     */
    public function print(): string
    {
        return isset($this->value) ? "$this->type: $this->value" : '';
    }
}
