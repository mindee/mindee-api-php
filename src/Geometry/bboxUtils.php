<?php

namespace Mindee\Geometry;

function generateBBoxFromPolygon(Polygon $polygon): ?BBox
{
    if (!$polygon) {
        return null;
    }

    return new BBox(
        getMinXCoordinate($polygon),
        getMaxXCoordinate($polygon),
        getMinYCoordinate($polygon),
        getMaxYCoordinate($polygon),
    );
}

function generateBBoxFromPolygons(array $polygons): ?BBox
{
    if (!$polygons) {
        return null;
    }

    $merged = $polygons[0];
    foreach ($polygons as $polygon) {
        $merged = merge($merged, $polygon);
    }

    return new BBox(
        getMinXCoordinate($merged),
        getMaxXCoordinate($merged),
        getMinYCoordinate($merged),
        getMaxYCoordinate($merged),
    );
}

function mergeBBoxes(array $bboxes)
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

    return new BBox($minX, $maxX, $minY, $maxY);
}
