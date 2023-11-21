<?php

namespace Mindee\Geometry;

class Polygon
{
    private ?array $coordinates;

    public function __construct(?array $coordinates = null)
    {
        $this->coordinates = $coordinates;
    }

    public function getCentroid(): Point
    {
        return PolygonUtils::getCentroid($this->coordinates);
    }

    public function isEmpty(): bool
    {
        return count($this->coordinates) == 0;
    }

    public function getCoordinates(): ?array
    {
        return $this->coordinates;
    }

    public function __toString()
    {
        if (!$this->isEmpty()) {
            return 'Polygon with ' . count($this->getCoordinates()) . ' points.';
        }

        return '';
    }
}
