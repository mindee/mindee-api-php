<?php

namespace Mindee\Geometry;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeGeometryException;

/**
 * Utility class for Polygon.
 */
abstract class PolygonUtils
{
    /**
     * Gets the centroid (Point) of a set of points.
     *
     * @param array $vertices Array of points.
     * @return Point
     */
    public static function getCentroid(array $vertices): Point
    {
        $verticesSum = count($vertices);

        $xSum = 0.0;
        $ySum = 0.0;

        foreach ($vertices as $vertex) {
            /* @var Point $vertex */
            $xSum += $vertex->getX();
            $ySum += $vertex->getY();
        }

        return new Point($xSum / $verticesSum, $ySum / $verticesSum);
    }

    /**
     * Compares two polygons on the Y axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param Polygon $polygon1 First polygon to compare.
     * @param Polygon $polygon2 Second polygon to compare.
     * @return integer
     */
    public static function compareOnY(Polygon $polygon1, Polygon $polygon2): int
    {
        $sort = ($polygon1->getMinY() - $polygon2->getMinY());
        if ($sort == 0) {
            return 0;
        }
        return $sort < 0 ? -1 : 1;
    }

    /**
     * Merges two polygons.
     *
     * @param Polygon $base   First polygon to merge.
     * @param Polygon $target Second polygon to merge.
     * @return Polygon
     * @throws MindeeGeometryException Throws if both polygons are empty.
     */
    public static function merge(Polygon $base, Polygon $target): Polygon
    {
        if ((!$base->getCoordinates()) && (!$target->getCoordinates())) {
            throw new MindeeGeometryException(
                'Cannot merge two empty polygons.',
                ErrorCode::GEOMETRIC_OPERATION_FAILED
            );
        }
        if (!$base->getCoordinates()) {
            return $target;
        }
        if (!$target->getCoordinates()) {
            return $base;
        }
        return new Polygon(
            array_unique(array_merge($base->getCoordinates(), $target->getCoordinates()), SORT_REGULAR)
        );
    }

    /**
     * Creates a bounding box from one or two polygons.
     *
     * @param Polygon      $base   First polygon.
     * @param Polygon|null $target Second polygon.
     * @return Polygon
     */
    public static function createBoundingBoxFrom(Polygon $base, ?Polygon $target = null): Polygon
    {
        if ($target) {
            $merged = PolygonUtils::merge($base, $target);
        } else {
            $merged = $base;
        }
        $topLeft = new Point($merged->getMinX(), $merged->getMinY());
        $topRight = new Point($merged->getMaxX(), $merged->getMinY());
        $bottomRight = new Point($merged->getMaxX(), $merged->getMaxY());
        $bottomLeft = new Point($merged->getMinX(), $merged->getMaxY());

        return new Polygon([
            $topLeft,
            $topRight,
            $bottomRight,
            $bottomLeft,
        ]);
    }

    /**
     * Generates a quadrilateral Polygon from a given prediction.
     *
     * @param array $prediction Raw prediction array.
     * @return Polygon
     * @throws MindeeGeometryException Throws if the polygon isn't a quadrilateral.
     */
    public static function quadrilateralFromPrediction(array $prediction): Polygon
    {
        if (count($prediction) != 4) {
            throw new MindeeGeometryException('Prediction must have exactly 4 points.');
        }
        return new Polygon($prediction);
    }

    /**
     * Checks whether a point is located within a coordinate range on the x-axis.
     *
     * @param Point $point Point to check.
     * @param float $minX  Lower bound.
     * @param float $maxX  Upper bound.
     * @return boolean
     */
    public static function isPointInX(Point $point, float $minX, float $maxX): bool
    {
        return $point->getX() >= $minX && $point->getX() <= $maxX;
    }

    /**
     * Checks whether a point is located within a coordinate range on the y-axis.
     *
     * @param Point $point Point to check.
     * @param float $minY  Lower bound.
     * @param float $maxY  Upper bound.
     * @return boolean
     */
    public static function isPointInY(Point $point, float $minY, float $maxY): bool
    {
        return $point->getY() >= $minY && $point->getY() <= $maxY;
    }
}
