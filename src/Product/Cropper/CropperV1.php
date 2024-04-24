<?php

/** Cropper V1. */

namespace Mindee\Product\Cropper;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Cropper API version 1 inference prediction.
 */
class CropperV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "cropper";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "1";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new CropperV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(CropperV1Page::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
