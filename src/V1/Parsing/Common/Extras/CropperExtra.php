<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Extras;

use Mindee\V1\Parsing\Standard\PositionField;

use function array_key_exists;

/**
 * Contains information on the cropping of a prediction.
 */
class CropperExtra
{
    /**
     * @var array<PositionField> List of all croppings coordiantes.
     */
    public array $croppings;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->croppings = [];
        if (array_key_exists("cropping", $rawPrediction) && isset($rawPrediction['cropping'])) {
            foreach ($rawPrediction['cropping'] as $cropping) {
                $this->croppings[] = new PositionField($cropping, $pageId);
            }
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $croppingsStr = [];
        foreach ($this->croppings as $cropping) {
            $croppingsStr[] = (string) $cropping;
        }
        return implode("\n           ", $croppingsStr);
    }
}
