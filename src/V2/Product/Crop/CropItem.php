<?php

namespace Mindee\V2\Product\Crop;

use Mindee\Parsing\V2\Field\FieldLocation;
use Mindee\Parsing\V2\InferenceResponse;

/**
 * Result of a cropped document region.
 */
class CropItem
{
    /**
     * @var FieldLocation Location which includes cropping coordinates for the detected object,
     * within the source document.
     */
    public FieldLocation $location;
    /**
     * @var string Type or classification of the detected object.
    */
    public string $objectType;

    /**
     * @var InferenceResponse|null $extractionResponse The extraction response associated with the crop.
     */
    public ?InferenceResponse $extractionResponse;
    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->location = new FieldLocation($rawResponse['location']);
        $this->objectType = $rawResponse['object_type'];
        $this->extractionResponse = isset($rawResponse['extraction_response']) ?
            new InferenceResponse($rawResponse['extraction_response']) : null;
    }

    /**
     * @return string String representation.
     */
    public function __toString()
    {
        return "* :Location: $this->location\n  :Object Type: $this->objectType";
    }
}
