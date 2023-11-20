<?php

namespace Mindee\Parsing\Standard;

use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;

trait FieldPositionMixin
{
    public Polygon $polygon;
    public ?Polygon $boundingBox;
    public ?int $page_id;

    protected function setPosition(array $raw_prediction)
    {
        $this->boundingBox = null;
        $this->polygon = new Polygon();
        if (array_key_exists('polygon', $raw_prediction)) {
            $points = [];
            foreach ($raw_prediction['polygon'] as $point) {
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
