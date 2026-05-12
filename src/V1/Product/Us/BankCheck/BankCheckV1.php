<?php

declare(strict_types=1);

/** Bank Check V1. */

namespace Mindee\V1\Product\Us\BankCheck;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Bank Check API version 1 inference prediction.
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
            try {
                $this->pages[] = new Page(BankCheckV1Page::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
