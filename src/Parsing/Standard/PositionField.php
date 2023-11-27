<?php

namespace Mindee\Parsing\Standard;

use Mindee\Error\MindeeGeometryException;
use Mindee\Geometry\Polygon;

use function Mindee\Geometry\polygon_from_prediction;
use function Mindee\Geometry\quadrilateral_from_prediction;

class PositionField extends BaseField
{
    public ?Polygon $value;
    public ?Polygon $polygon;
    public ?Polygon $quadrangle;
    public ?Polygon $rectangle;
    public ?Polygon $boundingBox;

    private static function getQuadrilateral(array $raw_prediction, string $key): ?Polygon
    {
        if (array_key_exists($key, $raw_prediction)) {
            return quadrilateral_from_prediction($raw_prediction[$key]);
        }

        return null;
    }

    private static function getPolygon(array $raw_prediction, string $key): ?Polygon
    {
        if (array_key_exists($key, $raw_prediction)) {
            $polygon = $raw_prediction[$key];
            try {
                polygon_from_prediction($polygon);
            } catch (MindeeGeometryException $exc) {
                return null;
            }
        }

        return null;
    }

    public function __construct(
        array $raw_prediction,
        ?int $page_id = null,
        bool $reconstructed = false,
        string $value_key = 'polygon'
    ) {
        parent::__construct($raw_prediction, $page_id, $reconstructed, $value_key);

        $this->boundingBox = PositionField::getQuadrilateral($raw_prediction, 'bounding_box');
        $this->quadrangle = PositionField::getQuadrilateral($raw_prediction, 'quadrangle');
        $this->rectangle = PositionField::getQuadrilateral($raw_prediction, 'rectangle');
        $this->polygon = PositionField::getPolygon($raw_prediction, 'polygon');

        $this->value = $this->polygon;
    }

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
