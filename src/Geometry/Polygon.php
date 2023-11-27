<?php

namespace Mindee\Geometry;

/**
 * Polygon represented as a set of coordinates (vertices/points).
 */
class Polygon
{
    /**
     * @var array|null Vertices of the polygon.
     */
    private ?array $coordinates;

    /**
     * @param array|null $coordinates Coordinates of the polygon as a set of Points.
     */
    public function __construct(?array $coordinates = null)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Retrieves the centroid of the polygon.
     *
     * @return \Mindee\Geometry\Point
     */
    public function getCentroid(): Point
    {
        return PolygonUtils::getCentroid($this->coordinates);
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
