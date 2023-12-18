<?php

namespace Mindee\Parsing\Common;

/**
 * Response of a prediction request.
 *
 * This is a generic class, so certain class properties depend on the document type.
 */
class PredictResponse extends ApiResponse
{
    /**
     * @var \Mindee\Parsing\Common\Document The document object, properly parsed after being retrieved from the server.
     */
    public Document $document;

    /**
     * @param string $predictionType Type of prediction.
     * @param array  $rawResponse    Raw HTTP response.
     */
    public function __construct(string $predictionType, array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->document = new Document($predictionType, $rawResponse['document']);
    }
}
