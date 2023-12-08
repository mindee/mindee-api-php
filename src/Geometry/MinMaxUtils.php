<?php

namespace Mindee\Geometry;

/**
 * Utility class for MinMax.
 */
class MinMaxUtils
{
    /**
     * Retrieves the upper and lower bounds of the y-axis from an array of points.
     *
     * @param array $points An array of points.
     * @return \Mindee\Geometry\MinMax
     */
    public static function getMinMaxY(array $points): MinMax
    {
        $yCoords = [];
        foreach ($points as $point) {
            $yCoords[] = $point->getY();
        }

        return new MinMax(min($yCoords), max($yCoords));
    }

    /**
     * Retrieves the upper and lower bounds of the x-axis from an array of points.
     *
     * @param array $points An array of points.
     * @return \Mindee\Geometry\MinMax
     */
    public static function getMinMaxX(array $points): MinMax
    {
        $xCoords = [];
        foreach ($points as $point) {
            $xCoords[] = $point->getX();
        }

        return new MinMax(min($xCoords), max($xCoords));
    }
}
