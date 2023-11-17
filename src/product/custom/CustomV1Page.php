<?php

namespace Mindee\product\custom;

use Mindee\parsing\common\Prediction;
use Mindee\parsing\custom\ListField;

class CustomV1Page extends Prediction
{
    public array $fields;

    function __construct(array $raw_prediction)
    {
        $this->fields = [];
        foreach ($raw_prediction as $field_name => $field_contents) {
            $this->fields[$field_name] = new ListField($field_contents);
        }
    }
}
