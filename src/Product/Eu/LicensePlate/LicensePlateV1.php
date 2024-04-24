<?php

/** License Plate V1. */

namespace Mindee\Product\Eu\LicensePlate;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * License Plate API version 1 inference prediction.
 */
class LicensePlateV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "license_plates";
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
        $this->prediction = new LicensePlateV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(LicensePlateV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
