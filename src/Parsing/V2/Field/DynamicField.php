<?php

namespace Mindee\Parsing\V2\Field;

trait DynamicField
{
    /**
     * @var array<FieldLocation> List of possible locations for a field.
     */
    public array $locations;

    /**
     * @param array   $rawPrediction Raw prediction.
     * @param integer $indentLevel   Indent level.
     */
    public function __construct(array $rawPrediction, int $indentLevel = 0)
    {
        if (array_key_exists("locations", $rawPrediction)) {
            $this->locations = [];
            foreach ($rawPrediction["locations"] as $location) {
                $this->locations[] = new FieldLocation($location);
            }
        }
    }
}
