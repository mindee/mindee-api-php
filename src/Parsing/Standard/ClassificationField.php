<?php

namespace Mindee\Parsing\Standard;

class ClassificationField extends BaseField
{
    use FieldConfidenceMixin;

    public $value;

    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'value'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);
    }
}
