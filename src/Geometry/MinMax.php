<?php

namespace Mindee\Geometry;

class MinMax
{
    private float $min;
    private float $max;

    public function __construct(float $min, float $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function getMax(): float
    {
        return $this->max;
    }
}
