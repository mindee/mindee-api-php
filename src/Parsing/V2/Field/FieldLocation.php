<?php

namespace Mindee\Parsing\V2\Field;

use Mindee\Geometry\Polygon;

/**
 * Location of a field.
 */
class FieldLocation
{
    /**
     * Free polygon made up of points (can be null when not provided).
     *
     * @var Polygon|null
     */
    public ?Polygon $polygon;

    /**
     * Page ID.
     *
     * @var integer|null
     */
    public ?int $page;

    /**
     * @param array $serverResponse Raw server response.
     */
    public function __construct(array $serverResponse)
    {
        $this->polygon = isset($serverResponse['polygon']) ? new Polygon($serverResponse['polygon']) : null;
        $this->page = isset($serverResponse['page']) && is_int($serverResponse['page'])
            ? $serverResponse['page']
            : null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->polygon ? (string)$this->polygon : '';
    }
}
