<?php

declare(strict_types=1);

namespace V2\Product\Extraction;

use Mindee\Input\PathInput;
use Mindee\V2\Client;
use Mindee\V2\Error\MindeeV2HttpException;
use Mindee\V2\Product\Extraction\RagDocuments\ExtractionRagAnnotationResponse;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentAnnotationParameters;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentUploadParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../../TestingUtilities.php");

/**
 * RAG Documents functional tests.
 */
class RagDocumentsFunctional extends TestCase
{
    private Client $client;
    private string $extractionModelId;

    protected function setUp(): void
    {
        $this->client = new Client(getenv('MINDEE_V2_API_KEY') ?: null);
        $this->extractionModelId = getenv('MINDEE_V2_FINDOC_MODEL_ID') ?: '';
    }

    public function testRagDocumentLifecycleMustSucceed(): void
    {
        $inputSource = new PathInput(
            TestingUtilities::getV2ProductDir() . '/extraction/financial_document/default_sample.jpg'
        );
        $parameters = new RagDocumentUploadParameters(modelId: $this->extractionModelId);

        $postResponse = $this->client->uploadAndGetRagDocumentPoll(
            ExtractionRagAnnotationResponse::class,
            $inputSource,
            $parameters
        );
        self::assertNotNull($postResponse);

        $postAnnotation = $postResponse->annotation;
        self::assertNotNull($postAnnotation->fields);

        $documentId = $postResponse->id;
        self::assertNotNull($documentId);

        self::assertEquals("Draft", $postResponse->status);

        $postAnnotation->fields->getSimpleField('supplier_name')->selected = true;
        $postAnnotation->fields->getSimpleField('supplier_name')->guidelines = "I am the walrus!";
        $postAnnotation->fields->getSimpleField('invoice_number')->selected = true;
        $postAnnotation->fields->getSimpleField('invoice_number')->guidelines = "koo koo katchoo!";

        $patchAnnotationResponse = $this->client->updateRagAnnotation(
            ExtractionRagAnnotationResponse::class,
            new RagDocumentAnnotationParameters(
                documentId: $documentId,
                annotation: $postAnnotation
            )
        );
        self::assertNotNull($patchAnnotationResponse);
        $patchAnnotation = $patchAnnotationResponse->annotation;
        self::assertEquals(
            "I am the walrus!",
            $patchAnnotation->fields->getSimpleField('supplier_name')->guidelines
        );
        self::assertTrue($patchAnnotation->fields->getSimpleField('supplier_name')->selected);
        self::assertEquals(
            "koo koo katchoo!",
            $patchAnnotation->fields->getSimpleField('invoice_number')->guidelines
        );
        self::assertTrue($patchAnnotation->fields->getSimpleField('invoice_number')->selected);

        $getResponse = $this->client->getReadyRagDocumentPoll(
            ExtractionRagAnnotationResponse::class,
            $documentId
        );
        self::assertNotNull($getResponse);
        $getAnnotation = $getResponse->annotation;
        self::assertNotNull($getAnnotation);

        self::assertEquals("Draft", $getResponse->status);

        self::assertEquals(
            "I am the walrus!",
            $getAnnotation->fields->getSimpleField('supplier_name')->guidelines
        );
        self::assertTrue($getAnnotation->fields->getSimpleField('supplier_name')->selected);
        self::assertEquals(
            "koo koo katchoo!",
            $getAnnotation->fields->getSimpleField('invoice_number')->guidelines
        );
        self::assertTrue($getAnnotation->fields->getSimpleField('invoice_number')->selected);

        $patchStatusResponse = $this->client->updateRagAnnotation(
            ExtractionRagAnnotationResponse::class,
            new RagDocumentAnnotationParameters(
                documentId: $documentId,
                status: "Active"
            )
        );
        self::assertNotNull($patchStatusResponse);
        self::assertEquals("Active", $patchStatusResponse->status);

        $deleteResponse = $this->client->deleteExtractionRagDocument($documentId);
        self::assertTrue($deleteResponse);

        $this->expectException(MindeeV2HttpException::class);
        $this->client->getRagDocument(ExtractionRagAnnotationResponse::class, $documentId);
    }
}
