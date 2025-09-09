<?php

namespace Geometry;

use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;
use PHPUnit\Framework\TestCase;
use TypeError;

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
        $this->assertEquals(0.123, $this->polygonWhichIsNotRectangle->getMinX());
    }

    public function testGivenAValidPolygonMustGetTheMinY()
    {
        $this->assertEquals(0.53, $this->polygonWhichIsNotRectangle->getMinY());
    }

    public function testGivenAValidPolygonMustGetTheMaxX()
    {
        $this->assertEquals(0.175, $this->polygonWhichIsNotRectangle->getMaxX());
    }

    public function testGivenAValidPolygonMustGetTheMaxY()
    {
        $this->assertEquals(0.546, $this->polygonWhichIsNotRectangle->getMaxY());
    }

    public function testMergePolygonsWithTwoNotNullMustGetAValidPolygon()
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, $this->polygon2);

        $this->assertEquals(0.442, $mergedPolygon->getMinY());
        $this->assertEquals(0.081, $mergedPolygon->getMinX());
        $this->assertEquals(0.451, $mergedPolygon->getMaxY());
        $this->assertEquals(0.26, $mergedPolygon->getMaxX());
    }

    public function testMergeWithNullPolygonMustThrow()
    {
        $this->expectException(TypeError::class);
        PolygonUtils::merge(null, null);
    }

    public function testMergeWith1PolygonAndANullPolygonMustGetPolygon()
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, new Polygon([]));

        $this->assertEquals(0.442, $mergedPolygon->getMinY());
        $this->assertEquals(0.081, $mergedPolygon->getMinX());
        $this->assertEquals(0.451, $mergedPolygon->getMaxY());
        $this->assertEquals(0.15, $mergedPolygon->getMaxX());
    }
}
