<?php

declare(strict_types=1);

namespace Geometry;

use Mindee\Geometry\BBox;
use Mindee\Geometry\BBoxUtils;
use Mindee\Geometry\Point;
use Mindee\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

class BBoxTest extends TestCase
{
    public function testWith0PolygonMustGetNull(): void
    {
        $polygon = new Polygon();
        $bbox = BBoxUtils::generateBBoxFromPolygon($polygon);
        self::assertNull($bbox);
    }

    public function testWith1PolygonAndANullPolygonMustGetPolygon(): void
    {
        $polygons = [];
        $polygons[] = new Polygon(
            [new Point(0.081, 0.442), new Point(0.15, 0.442), new Point(0.15, 0.451), new Point(0.081, 0.451)]
        );
        $polygons[] = null;
        $bbox = BBoxUtils::generateBBoxFromPolygons($polygons);

        self::assertSame(0.442, $bbox->getMinY());
        self::assertSame(0.081, $bbox->getMinX());
        self::assertSame(0.451, $bbox->getMaxY());
        self::assertSame(0.15, $bbox->getMaxX());
    }

    public function testWithOnePolygonMustGetValidBBox(): void
    {
        $polygon = new Polygon(
            [new Point(0.081, 0.442), new Point(0.15, 0.442), new Point(0.15, 0.451), new Point(0.081, 0.451)]
        );
        $bbox = BBoxUtils::generateBBoxFromPolygon($polygon);
        self::assertSame(0.442, $bbox->getMinY());
        self::assertSame(0.081, $bbox->getMinX());
        self::assertSame(0.451, $bbox->getMaxY());
        self::assertSame(0.15, $bbox->getMaxX());
    }

    public function testWithTwoPolygonsMustGetValidBBox(): void
    {
        $polygon1 = new Polygon(
            [new Point(0.081, 0.442), new Point(0.15, 0.442), new Point(0.15, 0.451), new Point(0.081, 0.451)]
        );
        $polygon2 = new Polygon(
            [new Point(0.157, 0.442), new Point(0.26, 0.442), new Point(0.26, 0.451), new Point(0.157, 0.451)]
        );
        $polygons = [$polygon1, $polygon2];
        $bbox = BBoxUtils::generateBBoxFromPolygons($polygons);
        self::assertSame(0.442, $bbox->getMinY());
        self::assertSame(0.081, $bbox->getMinX());
        self::assertSame(0.451, $bbox->getMaxY());
        self::assertSame(0.26, $bbox->getMaxX());
    }

    public function testMerge2BboxMustGetValidBBox(): void
    {
        $bbox1 = new BBox(0.081, 0.15, 0.442, 0.451);
        $bbox2 = new BBox(0.157, 0.26, 0.442, 0.451);
        $mergedBBoxes = BBoxUtils::mergeBBoxes([$bbox1, $bbox2]);
        self::assertSame(0.442, $mergedBBoxes->getMinY());
        self::assertSame(0.081, $mergedBBoxes->getMinX());
        self::assertSame(0.451, $mergedBBoxes->getMaxY());
        self::assertSame(0.26, $mergedBBoxes->getMaxX());
    }
}
