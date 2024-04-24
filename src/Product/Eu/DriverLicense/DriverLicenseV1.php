<?php

/** Driver License V1. */

namespace Mindee\Product\Eu\DriverLicense;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Driver License API version 1 inference prediction.
 */
class DriverLicenseV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "eu_driver_license";
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
        $this->prediction = new DriverLicenseV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(DriverLicenseV1Page::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
