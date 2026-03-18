<?php

namespace Mindee\V2\Product\Crop;

use Mindee\Parsing\V2\BaseResponse;

/**
 * Represent a crop response from Mindee V2 API.
 */
class CropResponse extends BaseResponse
{
    /**
     * @var CropInference Contents of the inference.
     */
    public CropInference $inference;

    /**
     * @var string Slug for the inference.
     */
    public static string $slug = "crop";

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->inference = new CropInference($rawResponse['inference']);
    }
}
