<?php

declare(strict_types=1);

/** Bank Account Details V1. */

namespace Mindee\V1\Product\Fr\BankAccountDetails;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Bank Account Details API version 1 inference prediction.
 */
class BankAccountDetailsV1 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "bank_account_details";
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
        $this->prediction = new BankAccountDetailsV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BankAccountDetailsV1Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
