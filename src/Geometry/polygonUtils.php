<?php

namespace Mindee\Geometry;

use Mindee\Error\MindeeGeometryException;

function getCentroid(array $vertices): Point
{
    $vertices_sum = count($vertices);

    $xSum = 0.0;
    $ySum = 0.0;

    foreach ($vertices as $vertex) {
        /* @var $vertex Point */
        $xSum += $vertex->getX();
        $ySum += $vertex->getY();
    }

    return new Point($xSum / $vertices_sum, $ySum / $vertices_sum);
}

function getMinYCoordinate(Polygon $polygon): float
{
    $min = null;
    foreach ($polygon->getCoordinates() as $point) {
        if (!isset($min) || $min > $point->getY()) {
            $min = $point->getY();
        }
    }
    if (!isset($min)) {
        throw new MindeeGeometryException('The provided polygon seems to be empty, or the Y coordinates of each point are invalid.');
    }

    return $min;
}

function getMinXCoordinate(Polygon $polygon): float
{
    $min = null;
    foreach ($polygon->getCoordinates() as $point) {
        if (!isset($min) || $min > $point->getX()) {
            $min = $point->getX();
        }
    }
    if (!isset($min)) {
        throw new MindeeGeometryException('The provided polygon seems to be empty, or the X coordinates of each point are invalid.');
    }

    return $min;
}

function getMaxYCoordinate(Polygon $polygon): float
{
    $min = null;
    foreach ($polygon->getCoordinates() as $point) {
        if (!isset($min) || $min < $point->getY()) {
            $min = $point->getY();
        }
    }
    if (!isset($min)) {
        throw new MindeeGeometryException('The provided polygon seems to be empty, or the Y coordinates of each point are invalid.');
    }

    return $min;
}

function getMaxXCoordinate(Polygon $polygon): float
{
    $min = null;
    foreach ($polygon->getCoordinates() as $point) {
        if (!isset($min) || $min < $point->getX()) {
            $min = $point->getX();
        }
    }
    if (!isset($min)) {
        throw new MindeeGeometryException('The provided polygon seems to be empty, or the X coordinates of each point are invalid.');
    }

    return $min;
}

function compareOnY(Polygon $polygon1, Polygon $polygon2): int
{
    $sort = getMinYCoordinate($polygon1) - getMinYCoordinate($polygon2);
    if ($sort == 0) {
        return 0;
    }

    return $sort < 0 ? -1 : 1;
}

function merge(?Polygon $base, ?Polygon $target): Polygon
{
    if (!$base && !$target) {
        throw new MindeeGeometryException('Cannot merge two empty polygons.');
    }
    if (!$base) {
        return $target;
    }
    if (!$target) {
        return $base;
    }

    return new Polygon(array_unique(array_merge($base->getCoordinates(), $target->getCoordinates())));
}

function createBoundingBoxFrom(?Polygon $base, ?Polygon $target = null): Polygon
{
    $merged = merge($base, $target);

    $top_left = new Point(getMinXCoordinate($merged), getMinYCoordinate($merged));
    $top_right = new Point(getMaxXCoordinate($merged), getMinYCoordinate($merged));
    $bottom_right = new Point(getMaxXCoordinate($merged), getMaxYCoordinate($merged));
    $bottom_left = new Point(getMinXCoordinate($merged), getMaxYCoordinate($merged));

    return new Polygon([
        $top_left,
        $top_right,
        $bottom_right,
        $bottom_left,
    ]);
}

function quadrilateral_from_prediction(array $prediction): Polygon
{
    if (count($prediction) != 4) {
        throw new MindeeGeometryException('Prediction must have exactly 4 points.');
    }

    return new Polygon([
        new Point($prediction[0][0], $prediction[0][1]),
        new Point($prediction[1][0], $prediction[1][1]),
        new Point($prediction[2][0], $prediction[2][1]),
        new Point($prediction[3][0], $prediction[3][1]),
    ]);
}

function polygon_from_prediction(array $prediction): Polygon
{
    $points = [];
    foreach ($prediction as $point) {
        $points[] = new Point($point[0], $point[1]);
    }

    return new Polygon($points);
}
