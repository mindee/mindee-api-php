<?php

/** Passport V1. */

namespace Mindee\Product\Passport;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;

/**
 * Inference prediction for Passport, API version 1.
 */
class PassportV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "passport";
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
        $this->prediction = new PassportV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            $pagePrediction = [];
            try {
                $pagePrediction = new Page(PassportV1Document::class, $page);
            } catch (\Exception $ignored) {
            }
            $this->pages[] = $pagePrediction;
        }
    }
}
