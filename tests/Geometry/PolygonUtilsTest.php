<?php

namespace Geometry;

use Mindee\Error\MindeeGeometryException;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;
use PHPUnit\Framework\TestCase;

class PolygonUtilsTest extends TestCase
{
    private Polygon $polygonWhichIsNotRectangle;
    private Polygon $polygon1;
    private Polygon $polygon2;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->polygonWhichIsNotRectangle = new Polygon(
            [new Point(0.123, 0.53), new Point(0.175, 0.53), new Point(0.175, 0.546), new Point(0.123, 0.546)]
        );

        $this->polygon1 = new Polygon(
            [new Point(0.081, 0.442), new Point(0.15, 0.442), new Point(0.15, 0.451), new Point(0.081, 0.451)]
        );
        $this->polygon2 = new Polygon(
            [new Point(0.157, 0.442), new Point(0.26, 0.442), new Point(0.26, 0.451), new Point(0.157, 0.451)]
        );
    }

    public function testGivenAValidPolygonMustGetTheValidCentroid()
    {
        $this->assertEquals($this->polygonWhichIsNotRectangle->getCentroid(), new Point(0.149, 0.538));
    }

    public function testGivenAValidPolygonMustGetTheMinX()
    {
        $this->assertEquals(0.123, PolygonUtils::getMinXCoordinate($this->polygonWhichIsNotRectangle));
    }

    public function testGivenAValidPolygonMustGetTheMinY()
    {
        $this->assertEquals(0.53, PolygonUtils::getMinYCoordinate($this->polygonWhichIsNotRectangle));
    }

    public function testGivenAValidPolygonMustGetTheMaxX()
    {
        $this->assertEquals(0.175, PolygonUtils::getMaxXCoordinate($this->polygonWhichIsNotRectangle));
    }

    public function testGivenAValidPolygonMustGetTheMaxY()
    {
        $this->assertEquals(0.546, PolygonUtils::getMaxYCoordinate($this->polygonWhichIsNotRectangle));
    }

    public function testMergePolygonsWithTwoNotNullMustGetAValidPolygon()
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, $this->polygon2);

        $this->assertEquals(0.442, PolygonUtils::getMinYCoordinate($mergedPolygon));
        $this->assertEquals(0.081, PolygonUtils::getMinXCoordinate($mergedPolygon));
        $this->assertEquals(0.451, PolygonUtils::getMaxYCoordinate($mergedPolygon));
        $this->assertEquals(0.26, PolygonUtils::getMaxXCoordinate($mergedPolygon));
    }

    public function testMergeWithNullPolygonMustThrow()
    {
        $this->expectException(MindeeGeometryException::class);
        PolygonUtils::merge(null, null);
    }

    public function testMergeWith1PolygonAndANullPolygonMustGetPolygon()
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, null);

        $this->assertEquals(0.442, PolygonUtils::getMinYCoordinate($mergedPolygon));
        $this->assertEquals(0.081, PolygonUtils::getMinXCoordinate($mergedPolygon));
        $this->assertEquals(0.451, PolygonUtils::getMaxYCoordinate($mergedPolygon));
        $this->assertEquals(0.15, PolygonUtils::getMaxXCoordinate($mergedPolygon));
    }
}
