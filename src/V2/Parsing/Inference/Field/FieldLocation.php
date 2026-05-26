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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response.
     */
    public function __construct(array $rawResponse)
    {
        $this->polygon = isset($rawResponse['polygon']) ? new Polygon($rawResponse['polygon']) : null;
        $this->page = isset($rawResponse['page']) && is_int($rawResponse['page'])
            ? $rawResponse['page']
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
