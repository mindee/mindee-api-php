<?php

/** Bank Account Details V2. */

namespace Mindee\V1\Product\Fr\BankAccountDetails;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Inference;
use Mindee\V1\Parsing\Common\Page;

/**
 * Bank Account Details API version 2 inference prediction.
 */
class BankAccountDetailsV2 extends Inference
{
    /**
     * @var string Name of the endpoint.
     */
    public static string $endpointName = "bank_account_details";
    /**
     * @var string Version of the endpoint.
     */
    public static string $endpointVersion = "2";

    /**
     * @param array $rawPrediction Raw prediction from the HTTP response.
     */
    public function __construct(array $rawPrediction)
    {
        parent::__construct($rawPrediction);
        $this->prediction = new BankAccountDetailsV2Document($rawPrediction['prediction']);
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $page) {
            try {
                $this->pages[] = new Page(BankAccountDetailsV2Document::class, $page);
            } catch (MindeeUnsetException) {
            }
        }
    }
}
