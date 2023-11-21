<?php

namespace Mindee\Parsing\Standard;

class CompanyRegistrationField extends BaseField
{
    use FieldPositionMixin;

    public string $type;

    public function __construct(
        array  $raw_prediction,
        ?int   $page_id = null,
        bool   $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
        $this->type = $raw_prediction['type'];
        $this->setPosition($raw_prediction);
    }

    public function print(): string
    {
        return isset($this->value) ? "$this->type: $this->value" : '';
    }
}
