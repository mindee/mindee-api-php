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
     * @param string $prediction_type Type of prediction.
     * @param array  $raw_response    Raw HTTP response.
     */
    public function __construct(string $prediction_type, array $raw_response)
    {
        parent::__construct($raw_response);
        $this->document = new Document($prediction_type, $raw_response['document']);
    }
}
