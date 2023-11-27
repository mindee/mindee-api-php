<?php

namespace Mindee\Geometry;

/**
 * Set of minimum and maximum values.
 */
class MinMax
{
    /**
     * @var float Minimum.
     */
    private float $min;
    /**
     * @var float Maximum.
     */
    private float $max;

    /**
     * @param float $min Input minimum.
     * @param float $max Input maximum.
     */
    public function __construct(float $min, float $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return float Retrieves the minimum.
     */
    public function getMin(): float
    {
        return $this->min;
    }

    /**
     * @return float Retrieves the maximum.
     */
    public function getMax(): float
    {
        return $this->max;
    }
}
