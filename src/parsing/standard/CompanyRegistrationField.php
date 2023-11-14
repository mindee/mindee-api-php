<?php

namespace Mindee\parsing\standard;

class CompanyRegistrationField extends BaseField
{
    use FieldPositionMixin;

    public string $type;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'value',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);
        $this->type = $raw_prediction['type'];
        $this->setPosition($raw_prediction);
    }

    public function print(): string
    {
        return isset($this->value) ? "$this->type: $this->value" : '';
    }
}
