<?php

namespace Mindee\Parsing\Common;

class Product
{
    public string $name;
    public string $version;

    public function __construct(array $raw_prediction)
    {
        $this->name = $raw_prediction['name'];
        $this->version = $raw_prediction['version'];
    }

    public function __toString()
    {
        return "$this->name v$this->version";
    }
}
