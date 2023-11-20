<?php

namespace Mindee\Geometry;

abstract class BBoxUtils
{
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

    function generateBBoxFromPolygons(array $polygons): ?BBox
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
