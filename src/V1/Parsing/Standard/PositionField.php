<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;

use function array_key_exists;
use function count;

/**
 * A field indicating a position or area on the document.
 * @extends BaseField<Polygon>
 */
class PositionField extends BaseField
{
    /**
     * @var Polygon|null Polygon of cropped area.
     */
    public ?Polygon $polygon;
    /**
     * @var Polygon|null Quadrangle of cropped area (does not exceed the canvas).
     */
    public ?Polygon $quadrangle;
    /**
     * @var Polygon|null Oriented rectangle of cropped area (may exceed the canvas).
     */
    public ?Polygon $rectangle;
    /**
     * @var Polygon|null Straight rectangle of cropped area (does not exceed the canvas).
     */
    public ?Polygon $boundingBox;

    /**
     * Retrieves the quadrilateral of a prediction.
     *
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param string $key Key to use for the value.
     */
    private static function getQuadrilateral(array $rawPrediction, string $key): ?Polygon
    {
        if (
            !array_key_exists($key, $rawPrediction)
            || $rawPrediction[$key] == null
            || $rawPrediction[$key] === []
        ) {
            return null;
        }

        return PolygonUtils::quadrilateralFromPrediction($rawPrediction[$key]);
    }

    /**
     * Retrieves the polygon of a prediction.
     *
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param string $key Key to use for the value.
     * @return Polygon|null
     */
    private static function getPolygon(array $rawPrediction, string $key): ?Polygon
    {
        if (!array_key_exists($key, $rawPrediction)) {
            return null;
        }

        return new Polygon($rawPrediction[$key]);
    }

    /**
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page id.
     * @param boolean $reconstructed Whether the field was reconstructed.
     * @param string $valueKey Key to use for the value.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null,
        bool $reconstructed = false,
        string $valueKey = 'polygon'
    ) {
        parent::__construct($rawPrediction, $pageId, $reconstructed, $valueKey);

        $this->boundingBox = self::getQuadrilateral($rawPrediction, 'bounding_box');
        $this->quadrangle = self::getQuadrilateral($rawPrediction, 'quadrangle');
        $this->rectangle = self::getQuadrilateral($rawPrediction, 'rectangle');
        $this->polygon = self::getPolygon($rawPrediction, 'polygon');

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
