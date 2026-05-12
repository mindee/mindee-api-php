<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use Mindee\Geometry\Polygon;

use function is_int;

/**
 * Location of a field.
 */
class FieldLocation
{
    /**
     * Free polygon made up of points (can be null when not provided).
     *
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
        return $this->polygon ? $this->polygon . " on page $this->page" : '';
    }
}
