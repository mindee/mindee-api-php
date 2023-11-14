<?php

namespace Mindee\parsing\standard;

use Mindee\error\MindeeGeometryException;
use Mindee\geometry\Polygon;
use function Mindee\geometry\polygon_from_prediction;
use function Mindee\geometry\quadrilateral_from_prediction;

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
        string $value_key = 'polygon',
        bool $reconstructed = false,
        ?int $page_id = null
    ) {
        parent::__construct($raw_prediction, $value_key, $reconstructed, $page_id);

        $this->boundingBox = PositionField::getQuadrilateral($raw_prediction, 'bounding_box');
        $this->quadrangle = PositionField::getQuadrilateral($raw_prediction, 'quadrangle');
        $this->rectangle = PositionField::getQuadrilateral($raw_prediction, 'rectangle');
        $this->polygon = PositionField::getPolygon($raw_prediction, 'polygon');

        $this->value = $this->polygon;
    }

    public function __toString(): string
    {
        if ($this->polygon) {
            return 'Polygon with '.count($this->polygon->getCoordinates()).' points.';
        }
        if ($this->boundingBox) {
            return 'Polygon with '.count($this->boundingBox->getCoordinates()).' points.';
        }
        if ($this->rectangle) {
            return 'Polygon with '.count($this->rectangle->getCoordinates()).' points.';
        }
        if ($this->quadrangle) {
            return 'Polygon with '.count($this->quadrangle->getCoordinates()).' points.';
        }

        return '';
    }
}
