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

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }
}
