<?php

namespace Mindee\Geometry;

/**
 * Bounding box represented as a set of minimum and maximum values for the x and y axes.
 */
class BBox
{
    /**
     * @var float Minimum X coordinate.
     */
    private float $minX;
    /**
     * @var float Maximum X coordinate.
     */
    private float $maxX;
    /**
     * @var float Minimum Y coordinate.
     */
    private float $minY;
    /**
     * @var float Maximum Y coordinate.
     */
    private float $maxY;

    /**
     * @param float $minX Input minimum X coordinate.
     * @param float $maxX Input maximum X coordinate.
     * @param float $minY Input minimum Y coordinate.
     * @param float $maxY Input maximum Y coordinate.
     */
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

    /**
     * Retrieves the minimum x coordinate.
     *
     * @return float
     */
    public function getMinX(): float
    {
        return $this->minX;
    }

    /**
     * Retrieves the maximum x coordinate.
     *
     * @return float
     */
    public function getMaxX(): float
    {
        return $this->maxX;
    }

    /**
     * Retrieves the minimum y coordinate.
     *
     * @return float
     */
    public function getMinY(): float
    {
        return $this->minY;
    }

    /**
     * Retrieves the maximum y coordinate.
     *
     * @return float
     */
    public function getMaxY(): float
    {
        return $this->maxY;
    }

    /**
     * Extends the BBox with the provided points.
     *
     * @param Polygon|array $points Series of points to add to the BBox.
     * @return void
     */
    public function extendWith($points)
    {
        if ($points instanceof Polygon) {
            $sequence = $points->getCoordinates();
        } else {
            $sequence = $points;
        }
        foreach ($sequence as $point) {
            if ($this->minX > $point->getX()) {
                $this->minX = $point->getX();
            }
            if ($this->minY > $point->getY()) {
                $this->minY = $point->getY();
            }
            if ($this->maxX < $point->getX()) {
                $this->maxX = $point->getX();
            }
            if ($this->maxY < $point->getY()) {
                $this->maxY = $point->getY();
            }
        }
    }
}
