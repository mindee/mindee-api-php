<?php

declare(strict_types=1);

namespace Geometry;

use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use Mindee\Geometry\PolygonUtils;
use PHPUnit\Framework\TestCase;
use TypeError;

class PolygonUtilsTest extends TestCase
{
    private readonly Polygon $polygonWhichIsNotRectangle;
    private readonly Polygon $polygon1;
    private readonly Polygon $polygon2;

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

    public function testGivenAValidPolygonMustGetTheValidCentroid(): void
    {
        self::assertEquals(new Point(0.149, 0.538), $this->polygonWhichIsNotRectangle->getCentroid());
    }

    public function testGivenAValidPolygonMustGetTheMinX(): void
    {
        self::assertSame(0.123, $this->polygonWhichIsNotRectangle->getMinX());
    }

    public function testGivenAValidPolygonMustGetTheMinY(): void
    {
        self::assertSame(0.53, $this->polygonWhichIsNotRectangle->getMinY());
    }

    public function testGivenAValidPolygonMustGetTheMaxX(): void
    {
        self::assertSame(0.175, $this->polygonWhichIsNotRectangle->getMaxX());
    }

    public function testGivenAValidPolygonMustGetTheMaxY(): void
    {
        self::assertSame(0.546, $this->polygonWhichIsNotRectangle->getMaxY());
    }

    public function testMergePolygonsWithTwoNotNullMustGetAValidPolygon(): void
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, $this->polygon2);

        self::assertSame(0.442, $mergedPolygon->getMinY());
        self::assertSame(0.081, $mergedPolygon->getMinX());
        self::assertSame(0.451, $mergedPolygon->getMaxY());
        self::assertSame(0.26, $mergedPolygon->getMaxX());
    }

    public function testMergeWithNullPolygonMustThrow(): void
    {
        $this->expectException(TypeError::class);
        PolygonUtils::merge(null, null);
    }

    public function testMergeWith1PolygonAndANullPolygonMustGetPolygon(): void
    {
        $mergedPolygon = PolygonUtils::merge($this->polygon1, new Polygon([]));

        self::assertSame(0.442, $mergedPolygon->getMinY());
        self::assertSame(0.081, $mergedPolygon->getMinX());
        self::assertSame(0.451, $mergedPolygon->getMaxY());
        self::assertSame(0.15, $mergedPolygon->getMaxX());
    }
}
