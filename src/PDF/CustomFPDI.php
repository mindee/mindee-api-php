<?php

namespace Mindee\PDF;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\FpdiTrait;

/**
 * Custom wrapper to add text rotation to FPDI.
 */
class CustomFPDI extends Fpdi
{
    use FpdiTrait;

    /**
     * @var integer Angle for the rotation.
     */
    protected $angle = 0;

    /**
     * Rotates the current drawing context.
     *
     * @param float $angle The rotation angle in degrees.
     * @param float $x     The x-coordinate of the rotation center. Default is current x position.
     * @param float $y     The y-coordinate of the rotation center. Default is current y position.
     * @return void
     */
    public function rotate(float $angle, float $x = -1, float $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }

        if (intval($angle) != 0) {
            $angle = -$angle;
        }
        $angle *= M_PI / 180;
        $c = cos($angle);
        $s = sin($angle);
        $cx = $x * $this->k;
        $cy = ($this->h - $y) * $this->k;
        $this->_out(
            sprintf(
                'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
                $c,
                $s,
                -$s,
                $c,
                $cx,
                $cy,
                -$cx,
                -$cy
            )
        );
    }

    /**
     * Ends the page, resetting any rotation.
     *
     * @return void
     */
    protected function _endpage() //phpcs:ignore
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
    /**
     * Starts a new transformation.
     *
     * @return void
     */
    public function startTransform()
    {
        $this->_out('q');
    }
    /**
     * Stops the current transformation.
     *
     * @return void
     */
    public function stopTransform()
    {
        $this->_out('Q');
    }
}
