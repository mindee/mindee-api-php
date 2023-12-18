<?php

/** Bank Account Details V1. */

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Parsing\Common\Inference;
use Mindee\Parsing\Common\Page;
use Mindee\Error\MindeeUnsetException;

/**
 * Inference prediction for Bank Account Details, API version 1.
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
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new BankAccountDetailsV1Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BankAccountDetailsV1Document::class, $page);
            } catch (MindeeUnsetException $ignored) {
            }
        }
    }
}
