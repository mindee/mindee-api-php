<?php

declare(strict_types=1);

namespace Mindee\Geometry;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeGeometryException;

use function count;

/**
 * Utility class for MinMax.
 */
class MinMaxUtils
{
    /**
     * Retrieves the upper and lower bounds of the y-axis from an array of points.
     *
     * @param array<Point>|Polygon $points An array of points.
     * @throws MindeeGeometryException Throws if the provided array is too small.
     */
    public static function getMinMaxY(mixed $points): MinMax
    {
        if (is_a(Polygon::class, $points)) {
            $points = $points->getCoordinates();
        }
        if (count($points) < 1) {
            throw new MindeeGeometryException(
                'The provided point array must have at least 1 point to calculate the Y bounds.',
                ErrorCode::GEOMETRIC_OPERATION_FAILED
            );
        }
        $yCoords = [];
        foreach ($points as $point) {
            $yCoords[] = $point->getY();
        }
        return new MinMax(min($yCoords), max($yCoords));
    }

    /**
     * Retrieves the upper and lower bounds of the x-axis from an array of points.
     *
     * @param array<Point>|Polygon $points An array of points.
     * @throws MindeeGeometryException Throws if the provided array is too small.
     */
    public static function getMinMaxX(mixed $points): MinMax
    {
        if (is_a(Polygon::class, $points)) {
            $points = $points->getCoordinates();
        }
        if (count($points) < 1) {
            throw new MindeeGeometryException(
                'The provided point array must have at least 1 point to calculate the X bounds.',
                ErrorCode::GEOMETRIC_OPERATION_FAILED
            );
        }
        $xCoords = [];
        foreach ($points as $point) {
            $xCoords[] = $point->getX();
        }
        return new MinMax(min($xCoords), max($xCoords));
    }
}
