<?php

/** Bank Check V1. */

namespace Mindee\Product\Us\BankCheck;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Bank Check, API version 1.
 */
class BankCheckV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "bank_check";
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
        $this->prediction = new BankCheckV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $pagePrediction = [];
            try {
                $pagePrediction = new Page(BankCheckV1Page::class, $page);
            } catch (\Exception $ignored) {
            }
            $this->pages[] = $pagePrediction;
        }
    }
}
