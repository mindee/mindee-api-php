<?php

namespace Mindee\parsing\custom;

class CustomLineV1
{
    public int $row_number;
    public array $fields;
    public $bbox; // TODO: geometry module

    public function __construct(
        array $raw_prediction
    ) {
    }
}
