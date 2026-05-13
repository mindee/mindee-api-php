<?php

declare(strict_types=1);

namespace Mindee\Geometry;

/**
 * Utility class for BBox.
 */
abstract class BBoxUtils
{
    /**
     * Generates a BBox from a polygon. Returns null if no polygon is provided.
     *
     * @param Polygon $polygon Polygon to get the BBox of.
     */
    public static function generateBBoxFromPolygon(Polygon $polygon): ?BBox
    {
        if (!$polygon->getCoordinates()) {
            return null;
        }
        return new BBox(
            $polygon->getMinX(),
            $polygon->getMaxX(),
            $polygon->getMinY(),
            $polygon->getMaxY(),
        );
    }

    /**
     * Generates a BBox from an array of polygons. Returns null if no polygons are provided.
     *
     * @param array<Polygon> $polygons Series of polygons to get the BBox of.
     */
    public static function generateBBoxFromPolygons(array $polygons): ?BBox
    {
        if (!$polygons) {
            return null;
        }
        $merged = null;
        foreach ($polygons as $polygon) {
            if (empty($polygon)) {
                continue;
            } else {
                if (empty($merged)) {
                    $merged = $polygon;
                }
            }
            if ($merged !== $polygon) {
                $merged = PolygonUtils::merge($merged, $polygon);
            }
        }
        return new BBox(
            $merged->getMinX(),
            $merged->getMaxX(),
            $merged->getMinY(),
            $merged->getMaxY(),
        );
    }

    /**
     * Merges an array of bboxes.
     *
     * @param array<BBox> $bboxes BBoxes to merge.
     */
    public static function mergeBBoxes(array $bboxes): ?BBox
    {
        if (!$bboxes) {
            return null;
        }
        $minX = null;
        $maxX = null;
        $minY = null;
        $maxY = null;
        foreach ($bboxes as $bbox) {
            if (!$minX || $minX > $bbox->getMinX()) {
                $minX = $bbox->getMinX();
            }
            if (!$minY || $minY > $bbox->getMinY()) {
                $minY = $bbox->getMinY();
            }
            if (!$maxX || $maxX < $bbox->getMaxX()) {
                $maxX = $bbox->getMaxX();
            }
            if (!$maxY || $maxY < $bbox->getMaxY()) {
                $maxY = $bbox->getMaxY();
            }
        }
        return new BBox((float) $minX, (float) $maxX, (float) $minY, (float) $maxY);
    }
}
