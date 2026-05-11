<?php

/** International ID V2. */

namespace Mindee\V1\Product\InternationalId;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * International ID API version 2 inference prediction.
 */
class InternationalIdV2 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "international_id";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "2";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new InternationalIdV2Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(InternationalIdV2Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
