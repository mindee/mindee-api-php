<?php

namespace Mindee\parsing\standard;

class ClassificationField extends BaseField
{
    use FieldConfidenceMixin;

    public string $value;

    public function __construct(
        array $raw_prediction,
        string $value_key = 'value',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);
    }
}
