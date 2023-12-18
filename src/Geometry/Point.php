<?php

namespace Mindee\Geometry;

/**
 * Representation of the coordinates of a point.
 */
class Point
{
    /**
     * @var float X coordinate.
     */
    private float $x;
    /**
     * @var float Y coordinate.
     */
    private float $y;

    /**
     * @param float $x Input x coordinate.
     * @param float $y Input y coordinate.
     */
    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Retrieves the x coordinate.
     *
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Retrieves the y coordinate.
     *
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }
}
