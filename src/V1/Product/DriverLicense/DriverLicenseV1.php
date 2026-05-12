<?php

declare(strict_types=1);

/** Driver License V1. */

namespace Mindee\V1\Product\DriverLicense;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Driver License API version 1 inference prediction.
 */
class DriverLicenseV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "driver_license";
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
                $this->pages[] = new Page(DriverLicenseV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
