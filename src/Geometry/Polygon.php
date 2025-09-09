<?php

namespace Mindee\Geometry;

/**
 * Polygon represented as a set of coordinates (vertices/points).
 */
class Polygon
{
    /**
     * @var Point[]|null Vertices of the polygon.
     */
    public ?array $coordinates;

    /**
     * @param array|null $coordinates Coordinates of the polygon as a set of Points.
     */
    public function __construct(?array $coordinates = null)
    {
        if (!is_null($coordinates)) {
            $this->coordinates = [];
            foreach ($coordinates as $point) {
                $this->coordinates[] = new Point($point[0], $point[1]);
            }
        } else {
            $this->coordinates = null;
        }
    }

    /**
     * Retrieves the centroid of the polygon.
     *
     * @return Point
     */
    public function getCentroid(): Point
    {
        return PolygonUtils::getCentroid($this->coordinates);
    }

    /**
     * Retrieves the upper and lower bounds of the y-axis.
     *
     * @return MinMax
     */
    public function getMinMaxY(): MinMax
    {
        return MinMaxUtils::getMinMaxY($this->coordinates);
    }

    /**
     * Retrieves the upper and lower bounds of the x-axis.
     *
     * @return MinMax
     */
    public function getMinMaxX(): MinMax
    {
        return MinMaxUtils::getMinMaxX($this->coordinates);
    }

    /**
     * Checks whether a point is located within the polygon's y-axis.
     *
     * @param Point $point Point to check.
     * @return boolean
     */
    public function isPointInY(Point $point): bool
    {
        $minMax = $this->getMinMaxY();
        return PolygonUtils::isPointInY($point, $minMax->getMin(), $minMax->getMax());
    }

    /**
     * Checks whether a point is located within the polygon's x-axis.
     *
     * @param Point $point Point to check.
     * @return boolean
     */
    public function isPointInX(Point $point): bool
    {
        $minMax = $this->getMinMaxX();
        return PolygonUtils::isPointInX($point, $minMax->getMin(), $minMax->getMax());
    }

    /**
     * Checks whether the Polygon has coordinates.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return count($this->coordinates) == 0;
    }

    /**
     * Retrieves the coordinates of the polygon.
     *
     * @return array|null
     */
    public function getCoordinates(): ?array
    {
        return $this->coordinates;
    }

    /**
     * @return string String representation.
     */
    public function __toString()
    {
        if (!$this->isEmpty()) {
            return 'Polygon with ' . count($this->getCoordinates()) . ' points.';
        }
        return '';
    }
}
