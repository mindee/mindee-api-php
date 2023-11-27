<?php

namespace Mindee\Geometry;

/**
 * Utility class for BBox.
 */
abstract class BBoxUtils
{
    /**
     * Generates a BBox from a polygon. Returns null if no polygon is provided.
     *
     * @param \Mindee\Geometry\Polygon|null $polygon Polygon to get the BBox of.
     * @return \Mindee\Geometry\BBox|null
     */
    public static function generateBBoxFromPolygon(?Polygon $polygon): ?BBox
    {
        if (!$polygon) {
            return null;
        }

        return new BBox(
            PolygonUtils::getMinXCoordinate($polygon),
            PolygonUtils::getMaxXCoordinate($polygon),
            PolygonUtils::getMinYCoordinate($polygon),
            PolygonUtils::getMaxYCoordinate($polygon),
        );
    }

    /**
     * Generates a BBox from an array of polygons. Returns null if no polygons are provided.
     *
     * @param array $polygons Series of polygons to get the BBox of.
     * @return \Mindee\Geometry\BBox|null
     */
    public static function generateBBoxFromPolygons(array $polygons): ?BBox
    {
        if (!$polygons) {
            return null;
        }

        $merged = $polygons[0];
        foreach ($polygons as $polygon) {
            $merged = PolygonUtils::merge($merged, $polygon);
        }

        return new BBox(
            PolygonUtils::getMinXCoordinate($merged),
            PolygonUtils::getMaxXCoordinate($merged),
            PolygonUtils::getMinYCoordinate($merged),
            PolygonUtils::getMaxYCoordinate($merged),
        );
    }

    /**
     * Merges an array of bboxes.
     *
     * @param array $bboxes BBoxes to merge.
     * @return \Mindee\Geometry\BBox|null
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
            if (!$minX || $minX > $bbox->minX) {
                $minX = $bbox->minX;
            }
            if (!$minY || $minY > $bbox->minY) {
                $minY = $bbox->minY;
            }
            if (!$maxX || $maxX > $bbox->maxX) {
                $maxX = $bbox->maxX;
            }
            if (!$maxY || $maxY > $bbox->maxY) {
                $maxY = $bbox->maxY;
            }
        }

        return new BBox((float)$minX, (float)$maxX, (float)$minY, (float)$maxY);
    }
}
