<?php

namespace Mindee\Parsing\Standard;

use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;

/**
 * Mixin trait to add position information.
 */
trait FieldPositionMixin
{
    /**
     * @var \Mindee\Geometry\Polygon A polygon containing the word in the document.
     */
    public Polygon $polygon;
    /**
     * @var \Mindee\Geometry\Polygon|null A right rectangle containing the word in the document.
     */
    public ?Polygon $boundingBox;

    /**
     * Sets the position of a field.
     *
     * @param array $rawPrediction Raw prediction array.
     * @return void
     */
    protected function setPosition(array $rawPrediction)
    {
        $this->boundingBox = null;
        $this->polygon = new Polygon();
        if (array_key_exists('polygon', $rawPrediction) and isset($rawPrediction['polygon'])) {
            $points = [];
            foreach ($rawPrediction['polygon'] as $point) {
                $points[] = new Point($point[0], $point[1]);
            }
            $this->polygon = new Polygon($points);
        }
        if ($this->polygon->getCoordinates()) {
            $this->boundingBox = PolygonUtils::createBoundingBoxFrom($this->polygon);
        } else {
            $this->boundingBox = null;
        }
    }
}
