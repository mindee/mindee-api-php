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
     * @var MinMax Min and max Y values of the polygon.
     */
    private MinMax $minMaxY;

    /**
     * @var MinMax Min and max X values of the polygon.
     */
    private MinMax $minMaxX;

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
        if (!isset($this->minMaxY)) {
            $this->minMaxY = MinMaxUtils::getMinMaxY($this->coordinates);
        }
        return $this->minMaxY;
    }

    /**
     * Retrieves the upper and lower bounds of the x-axis.
     *
     * @return MinMax
     */
    public function getMinMaxX(): MinMax
    {
        if (!isset($this->minMaxX)) {
            $this->minMaxX = MinMaxUtils::getMinMaxX($this->coordinates);
        }
        return $this->minMaxX;
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
     * Retrieves the minimum X coordinate.
     *
     * @return float
     */
    public function getMinX(): float
    {
        return $this->getMinMaxX()->getMin();
    }

    /**
     * Retrieves the maximum X coordinate.
     *
     * @return float
     */
    public function getMaxX(): float
    {
        return $this->getMinMaxX()->getMax();
    }

    /**
     * Retrieves the minimum Y coordinate.
     *
     * @return float
     */
    public function getMinY(): float
    {
        return $this->getMinMaxY()->getMin();
    }

    /**
     * Retrieves the maximum Y coordinate.
     *
     * @return float
     */
    public function getMaxY(): float
    {
        return $this->getMinMaxY()->getMax();
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
