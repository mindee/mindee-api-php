<?php

declare(strict_types=1);

namespace Mindee\Geometry;

/**
 * Set of minimum and maximum values.
 */
class MinMax
{
    /**
     * @param float $min Input minimum.
     * @param float $max Input maximum.
     */
    public function __construct(private readonly float $min, private readonly float $max) {}

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
