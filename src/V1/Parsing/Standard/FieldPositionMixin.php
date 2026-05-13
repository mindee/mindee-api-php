<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;

use function array_key_exists;

/**
 * Mixin trait to add position information.
 */
trait FieldPositionMixin
{
    /**
     * @var Polygon A polygon containing the word in the document.
     */
    public Polygon $polygon;
    /**
     * @var Polygon|null A right rectangle containing the word in the document.
     */
    public ?Polygon $boundingBox;

    /**
     * Sets the position of a field.
     *
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     */
    protected function setPosition(array $rawPrediction): void
    {
        $this->boundingBox = null;
        $this->polygon = new Polygon();
        if (array_key_exists('polygon', $rawPrediction) && isset($rawPrediction['polygon'])) {
            $this->polygon = new Polygon($rawPrediction['polygon']);
        }
        if ($this->polygon->getCoordinates()) {
            $this->boundingBox = PolygonUtils::createBoundingBoxFrom($this->polygon);
        } else {
            $this->boundingBox = null;
        }
    }
}
