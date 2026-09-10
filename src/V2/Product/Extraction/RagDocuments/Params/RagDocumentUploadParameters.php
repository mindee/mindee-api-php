<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments\Params;

use Mindee\V2\ClientOptions\BaseRagDocumentUploadParameters;
use Mindee\V2\Product\Extraction\RagDocuments\ExtractionRagAnnotationResponse;

/**
 * Upload parameters for RAG documents.
 * @extends BaseRagDocumentUploadParameters<ExtractionRagAnnotationResponse>
 */
class RagDocumentUploadParameters extends BaseRagDocumentUploadParameters
{
    /**
     * @var class-string<ExtractionRagAnnotationResponse> Response class.
     */
    protected static string $responseClass = ExtractionRagAnnotationResponse::class;
}
