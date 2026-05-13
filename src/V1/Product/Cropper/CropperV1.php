<?php

declare(strict_types=1);

/** Cropper V1. */

namespace Mindee\V1\Product\Cropper;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

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
     * @param array<string, mixed> $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new CropperV1Document();
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(CropperV1Page::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
