<?php

namespace Mindee\geometry;

class BBox
{
    private float $minX;
    private float $maxX;
    private float $minY;
    private float $maxY;

    public function __construct(
        float $minX,
        float $maxX,
        float $minY,
        float $maxY
    ) {
        $this->minX = $minX;
        $this->maxX = $maxX;
        $this->minY = $minY;
        $this->maxY = $maxY;
    }

    public function getMinX(): float
    {
        return $this->minX;
    }

    public function getMaX(): float
    {
        return $this->maxX;
    }

    public function getMinY(): float
    {
        return $this->minY;
    }

    public function getMaxY(): float
    {
        return $this->maxY;
    }

    public function extendWith($points)
    {
        if ($points instanceof Polygon) {
            $sequence = $points->getCoordinates();
        } else {
            $sequence = $points;
        }
        foreach ($sequence as $point) {
            if ($this->minX > $point->x) {
                $this->minX = $point->x;
            }
            if ($this->minY > $point->y) {
                $this->minY = $point->y;
            }
            if ($this->maxX < $point->x) {
                $this->maxX = $point->x;
            }
            if ($this->maxY < $point->y) {
                $this->maxY = $point->y;
            }
        }
    }
}
