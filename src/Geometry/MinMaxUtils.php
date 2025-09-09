<?php

namespace Mindee\Geometry;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeGeometryException;

/**
 * Utility class for MinMax.
 */
class MinMaxUtils
{
    /**
     * Retrieves the upper and lower bounds of the y-axis from an array of points.
     *
     * @param array $points An array of points.
     * @return MinMax
     * @throws MindeeGeometryException Throws if the provided array is too small.
     */
    public static function getMinMaxY(array $points): MinMax
    {
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
     * @param array $points An array of points.
     * @return MinMax
     * @throws MindeeGeometryException Throws if the provided array is too small.
     */
    public static function getMinMaxX(array $points): MinMax
    {
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
