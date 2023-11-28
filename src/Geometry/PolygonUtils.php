<?php

namespace Mindee\Geometry;

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
     * @return \Mindee\Geometry\Point
     */
    public static function getCentroid(array $vertices): Point
    {
        $vertices_sum = count($vertices);

        $xSum = 0.0;
        $ySum = 0.0;

        foreach ($vertices as $vertex) {
            /* @var Point $vertex */
            $xSum += $vertex->getX();
            $ySum += $vertex->getY();
        }

        return new Point($xSum / $vertices_sum, $ySum / $vertices_sum);
    }

    /**
     * Retrieves the minimum y coordinate of a Polygon.
     *
     * @param \Mindee\Geometry\Polygon $polygon Polygon to get the minimum y coordinate of.
     * @return float
     * @throws \Mindee\Error\MindeeGeometryException Throws if a minimum y-axis value cannot
     * be found, e.g. if the polygon is empty.
     */
    public static function getMinYCoordinate(Polygon $polygon): float
    {
        $min = null;
        foreach ($polygon->getCoordinates() as $point) {
            if (!isset($min) || $min > $point->getY()) {
                $min = $point->getY();
            }
        }
        if (!isset($min)) {
            throw new MindeeGeometryException(
                'The provided polygon seems to be empty, or the Y coordinates of each point are invalid.'
            );
        }

        return $min;
    }

    /**
     * Retrieves the minimum x coordinate of a Polygon.
     *
     * @param \Mindee\Geometry\Polygon $polygon Polygon to get the minimum y coordinate of.
     * @return float
     * @throws \Mindee\Error\MindeeGeometryException Throws if a minimum x-axis value cannot be
     * found, e.g. if the polygon is empty.
     */
    public static function getMinXCoordinate(Polygon $polygon): float
    {
        $min = null;
        foreach ($polygon->getCoordinates() as $point) {
            if (!isset($min) || $min > $point->getX()) {
                $min = $point->getX();
            }
        }
        if (!isset($min)) {
            throw new MindeeGeometryException(
                'The provided polygon seems to be empty, or the X coordinates of each point are invalid.'
            );
        }

        return $min;
    }

    /**
     * Retrieves the maximum y coordinate of a Polygon.
     *
     * @param \Mindee\Geometry\Polygon $polygon Polygon to get the minimum y coordinate of.
     * @return float
     * @throws \Mindee\Error\MindeeGeometryException Throws if a maximum y-axis value cannot be
     * found, e.g. if the polygon is empty.
     */
    public static function getMaxYCoordinate(Polygon $polygon): float
    {
        $min = null;
        foreach ($polygon->getCoordinates() as $point) {
            if (!isset($min) || $min < $point->getY()) {
                $min = $point->getY();
            }
        }
        if (!isset($min)) {
            throw new MindeeGeometryException(
                'The provided polygon seems to be empty, or the Y coordinates of each point are invalid.'
            );
        }

        return $min;
    }

    /**
     * Retrieves the maximum x coordinate of a Polygon.
     *
     * @param \Mindee\Geometry\Polygon $polygon Polygon to get the minimum y coordinate of.
     * @return float
     * @throws \Mindee\Error\MindeeGeometryException Throws if a maximum x-axis value cannot be
     * found, e.g. if the polygon is empty.
     */
    public static function getMaxXCoordinate(Polygon $polygon): float
    {
        $min = null;
        foreach ($polygon->getCoordinates() as $point) {
            if (!isset($min) || $min < $point->getX()) {
                $min = $point->getX();
            }
        }
        if (!isset($min)) {
            throw new MindeeGeometryException(
                'The provided polygon seems to be empty, or the X coordinates of each point are invalid.'
            );
        }

        return $min;
    }

    /**
     * Compares two polygons on the Y axis. Returns a sort-compliant result (0;-1;1).
     *
     * @param \Mindee\Geometry\Polygon $polygon1 First polygon to compare.
     * @param \Mindee\Geometry\Polygon $polygon2 Second polygon to compare.
     * @return integer
     */
    public static function compareOnY(Polygon $polygon1, Polygon $polygon2): int
    {
        $sort = self::getMinYCoordinate($polygon1) - self::getMinYCoordinate($polygon2);
        if ($sort == 0) {
            return 0;
        }

        return $sort < 0 ? -1 : 1;
    }

    /**
     * Merges two polygons.
     *
     * @param \Mindee\Geometry\Polygon|null $base   First polygon to merge.
     * @param \Mindee\Geometry\Polygon|null $target Second polygon to merge.
     * @return \Mindee\Geometry\Polygon
     * @throws \Mindee\Error\MindeeGeometryException Throws if both polygons are empty.
     */
    public static function merge(?Polygon $base, ?Polygon $target): Polygon
    {
        if (!$base && !$target) {
            throw new MindeeGeometryException('Cannot merge two empty polygons.');
        }
        if (!$base) {
            return $target;
        }
        if (!$target) {
            return $base;
        }

        return new Polygon(array_unique(array_merge($base->getCoordinates(), $target->getCoordinates())));
    }

    /**
     * Creates a bounding box from one or two polygons.
     *
     * @param \Mindee\Geometry\Polygon|null $base   First polygon.
     * @param \Mindee\Geometry\Polygon|null $target Second polygon.
     * @return \Mindee\Geometry\Polygon
     */
    public static function createBoundingBoxFrom(?Polygon $base, ?Polygon $target = null): Polygon
    {
        $merged = PolygonUtils::merge($base, $target);

        $top_left = new Point(self::getMinXCoordinate($merged), self::getMinYCoordinate($merged));
        $top_right = new Point(self::getMaxXCoordinate($merged), self::getMinYCoordinate($merged));
        $bottom_right = new Point(self::getMaxXCoordinate($merged), self::getMaxYCoordinate($merged));
        $bottom_left = new Point(self::getMinXCoordinate($merged), self::getMaxYCoordinate($merged));

        return new Polygon([
            $top_left,
            $top_right,
            $bottom_right,
            $bottom_left,
        ]);
    }

    /**
     * Generates a quadrilateral Polygon from a given prediction.
     *
     * @param array $prediction Raw prediction array.
     * @return \Mindee\Geometry\Polygon
     * @throws \Mindee\Error\MindeeGeometryException Throws if the polygon isn't a quadrilateral.
     */
    public static function quadrilateralFromPrediction(array $prediction): Polygon
    {
        if (count($prediction) != 4) {
            throw new MindeeGeometryException('Prediction must have exactly 4 points.');
        }

        return new Polygon([
            new Point($prediction[0][0], $prediction[0][1]),
            new Point($prediction[1][0], $prediction[1][1]),
            new Point($prediction[2][0], $prediction[2][1]),
            new Point($prediction[3][0], $prediction[3][1]),
        ]);
    }

    /**
     * Generates a Polygon from a given prediction.
     *
     * @param array $prediction Raw prediction array.
     * @return \Mindee\Geometry\Polygon
     */
    public static function polygonFromPrediction(array $prediction): Polygon
    {
        $points = [];
        foreach ($prediction as $point) {
            $points[] = new Point($point[0], $point[1]);
        }

        return new Polygon($points);
    }

    /**
     * Checks whether a point is located within a coordinate range on the x-axis.
     *
     * @param \Mindee\Geometry\Point $point Point to check.
     * @param float                  $min_x Lower bound.
     * @param float                  $max_x Upper bound.
     * @return boolean
     */
    public static function isPointInX(Point $point, float $min_x, float $max_x): bool
    {
        return $point->getX() >= $min_x && $point->getX() <= $max_x;
    }

    /**
     * Checks whether a point is in a polygon's x-axis range.
     *
     * @param \Mindee\Geometry\Point   $point   Point to check.
     * @param \Mindee\Geometry\Polygon $polygon Polygon.
     * @return boolean
     */
    public static function isPointInPolygonX(Point $point, Polygon $polygon): bool
    {
        $min_x = MinMaxUtils::getMinMaxX($polygon->getCoordinates())->getMin();
        $max_x = MinMaxUtils::getMinMaxX($polygon->getCoordinates())->getMax();
        return self::isPointInX($point, $min_x, $max_x);
    }

    /**
     * Checks whether a point is located within a coordinate range on the y-axis.
     *
     * @param \Mindee\Geometry\Point $point Point to check.
     * @param float                  $min_y Lower bound.
     * @param float                  $max_y Upper bound.
     * @return boolean
     */
    public static function isPointInY(Point $point, float $min_y, float $max_y): bool
    {
        return $point->getY() >= $min_y && $point->getY() <= $max_y;
    }

    /**
     * Checks whether a point is in a polygon's y-axis range.
     *
     * @param \Mindee\Geometry\Point   $point   Point to check.
     * @param \Mindee\Geometry\Polygon $polygon Polygon.
     * @return boolean
     */
    public static function isPointInPolygonY(Point $point, Polygon $polygon): bool
    {
        $min_y = MinMaxUtils::getMinMaxY($polygon->getCoordinates())->getMin();
        $max_y = MinMaxUtils::getMinMaxY($polygon->getCoordinates())->getMax();
        return self::isPointInY($point, $min_y, $max_y);
    }
}
