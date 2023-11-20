<?php

namespace Mindee\Geometry;


class MinMaxUtils
{
    public static function get_min_max_y(array $points): MinMax
    {
        $y_coords = [];
        foreach ($points as $point) {
            $y_coords[] = $point->y;
        }

        return new MinMax(min($y_coords), max($y_coords));
    }

    public static function get_min_max_x(array $points): MinMax
    {
        $x_coords = [];
        foreach ($points as $point) {
            $x_coords[] = $point->x;
        }

        return new MinMax(min($x_coords), max($x_coords));
    }
}
