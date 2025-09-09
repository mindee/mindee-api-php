<?php

namespace Mindee\Geometry;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Representation of the coordinates of a point.
 */
class Point implements ArrayAccess
{
    /**
     * @var float X coordinate.
     */
    private float $x;
    /**
     * @var float Y coordinate.
     */
    private float $y;

    /**
     * @param float $x Input x coordinate.
     * @param float $y Input y coordinate.
     */
    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Retrieves the x coordinate.
     *
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Retrieves the y coordinate.
     *
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     *  Whether an offset exists.
     * @param integer|string $offset Use 0 or 1.
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        if ($offset === 0 || $offset === 1) {
            return true;
        }
        return false;
    }

    /**
     *  Get an offset value.
     * @param integer|string $offset Use 0 or 1.
     * @return float
     * @throws InvalidArgumentException If the offset is not 0 or 1.
     */
    public function offsetGet($offset): float
    {
        if ($offset === 0) {
            return $this->x;
        } elseif ($offset === 1) {
            return $this->y;
        }
        throw new InvalidArgumentException("Use 0 for X or 1 for Y");
    }

    /**
     *  Set an offset value.
     * @param integer|string       $offset Use 0 or 1.
     * @param float|integer|string $value  Coordinate value to set.
     * @return void
     * @throws InvalidArgumentException If the offset is not 0 or 1.
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === 0) {
            $this->x = $value;
        } elseif ($offset === 1) {
            $this->y = $value;
        } else {
            throw new InvalidArgumentException("Use 0 for X or 1 for Y");
        }
    }

    /**
     *  Get an offset value.
     * @param integer|string $offset Use 0 or 1.
     * @return void
     * @throws InvalidArgumentException If the offset is not 0 or 1.
     */
    public function offsetUnset($offset): void
    {
        if ($offset === 0) {
            unset($this->x);
        } elseif ($offset === 1) {
            unset($this->y);
        } else {
            throw new InvalidArgumentException("Use 0 for X or 1 for Y");
        }
    }
}
