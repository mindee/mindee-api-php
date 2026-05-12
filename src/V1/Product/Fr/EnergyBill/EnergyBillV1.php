<?php

declare(strict_types=1);

/** Energy Bill V1. */

namespace Mindee\V1\Product\Fr\EnergyBill;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Energy Bill API version 1 inference prediction.
 */
class EnergyBillV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "energy_bill_fra";
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
        $this->prediction = new EnergyBillV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(EnergyBillV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
