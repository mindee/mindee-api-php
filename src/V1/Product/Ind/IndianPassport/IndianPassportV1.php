<?php

declare(strict_types=1);

/** Passport - India V1. */

namespace Mindee\V1\Product\Ind\IndianPassport;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Passport - India API version 1 inference prediction.
 */
class IndianPassportV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "ind_passport";
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
        $this->prediction = new IndianPassportV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(IndianPassportV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
