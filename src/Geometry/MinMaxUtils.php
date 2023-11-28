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
        $y_coords = [];
        foreach ($points as $point) {
            $y_coords[] = $point->y;
        }

        return new MinMax(min($y_coords), max($y_coords));
    }

    /**
     * Retrieves the upper and lower bounds of the x-axis from an array of points.
     *
     * @param array $points An array of points.
     * @return \Mindee\Geometry\MinMax
     */
    public static function getMinMaxX(array $points): MinMax
    {
        $x_coords = [];
        foreach ($points as $point) {
            $x_coords[] = $point->x;
        }

        return new MinMax(min($x_coords), max($x_coords));
    }
}
