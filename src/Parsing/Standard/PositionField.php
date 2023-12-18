<?php

namespace Mindee\Parsing\Standard;

use Mindee\Error\MindeeGeometryException;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;

/**
 * A field indicating a position or area on the document.
 */
class PositionField extends BaseField
{
    /**
     * @var \Mindee\Geometry\Polygon|null Polygon of cropped area, identical to the `polygon` property.
     */
    public $value;
    /**
     * @var \Mindee\Geometry\Polygon|null Polygon of cropped area.
     */
    public ?Polygon $polygon;
    /**
     * @var \Mindee\Geometry\Polygon|null Quadrangle of cropped area (does not exceed the canvas).
     */
    public ?Polygon $quadrangle;
    /**
     * @var \Mindee\Geometry\Polygon|null Oriented rectangle of cropped area (may exceed the canvas).
     */
    public ?Polygon $rectangle;
    /**
     * @var \Mindee\Geometry\Polygon|null Straight rectangle of cropped area (does not exceed the canvas).
     */
    public ?Polygon $boundingBox;

    /**
     * Retrieves the quadrilateral of a prediction.
     *
     * @param array  $rawPrediction Raw prediction array.
     * @param string $key           Key to use for the value.
     * @return \Mindee\Geometry\Polygon|null
     */
    private static function getQuadrilateral(array $rawPrediction, string $key): ?Polygon
    {
        if (array_key_exists($key, $rawPrediction) && $rawPrediction[$key] != null) {
            return PolygonUtils::quadrilateralFromPrediction($rawPrediction[$key]);
        }

        return null;
    }

    /**
     * Retrieves the polygon of a prediction.
     *
     * @param array  $rawPrediction Raw prediction array.
     * @param string $key           Key to use for the value.
     * @return \Mindee\Geometry\Polygon|null
     */
    private static function getPolygon(array $rawPrediction, string $key): ?Polygon
    {
        if (array_key_exists($key, $rawPrediction)) {
            return PolygonUtils::polygonFromPrediction($rawPrediction[$key]);
        }

        return null;
    }

    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page id.
     * @param boolean      $reconstructed Whether the field was reconstructed.
     * @param string       $valueKey      Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'polygon'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);

        $this->boundingBox = PositionField::getQuadrilateral($rawPrediction, 'bounding_box');
        $this->quadrangle = PositionField::getQuadrilateral($rawPrediction, 'quadrangle');
        $this->rectangle = PositionField::getQuadrilateral($rawPrediction, 'rectangle');
        $this->polygon = PositionField::getPolygon($rawPrediction, 'polygon');

        $this->value = $this->polygon;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        if ($this->polygon) {
            return 'Polygon with ' . count($this->polygon->getCoordinates()) . ' points.';
        }
        if ($this->boundingBox) {
            return 'Polygon with ' . count($this->boundingBox->getCoordinates()) . ' points.';
        }
        if ($this->rectangle) {
            return 'Polygon with ' . count($this->rectangle->getCoordinates()) . ' points.';
        }
        if ($this->quadrangle) {
            return 'Polygon with ' . count($this->quadrangle->getCoordinates()) . ' points.';
        }

        return '';
    }
}
