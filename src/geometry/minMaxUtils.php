<?php

namespace Mindee\geometry;

function get_min_max_y(array $points): MinMax
{
    $y_coords = [];
    foreach ($points as $point) {
        array_push($y_coords, $point->y);
    }

    return new MinMax(min($y_coords), max($y_coords));
}

function get_min_max_x(array $points): MinMax
{
    $x_coords = [];
    foreach ($points as $point) {
        array_push($x_coords, $point->x);
    }

    return new MinMax(min($x_coords), max($x_coords));
}
