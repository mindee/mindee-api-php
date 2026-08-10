<?php

declare(strict_types=1);

namespace V2\Product\Extraction;

use Mindee\V2\Product\Extraction\RagDocuments\ExtractionRagAnnotationResponse;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentAnnotationParameters;
use Mindee\V2\Product\Extraction\RagDocuments\Params\RagDocumentUploadParameters;
use Mindee\V2\Product\Extraction\RagDocuments\RagAnnotation;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../../TestingUtilities.php");

/**
 * RAG Documents unit tests.
 */
class RagDocumentsTest extends TestCase
{
    private function getResponse(string $filename): ExtractionRagAnnotationResponse
    {
        $fullPath = TestingUtilities::getV2DataDir()
            . "/products/extraction/rag_documents/"
            . $filename;
        $json = json_decode(file_get_contents($fullPath), true);
        return new ExtractionRagAnnotationResponse($json);
    }

    public function testPostParametersMustInit(): void
    {
        $parameters = new RagDocumentUploadParameters(modelId: "invalid-model-id");
        $reqParams = $parameters->getRequestParameters();
        self::assertEquals("invalid-model-id", $reqParams['model_id']);
    }

    public function testPatchParametersMustInit(): void
    {
        $annotation = new RagAnnotation([]);
        $parameters = new RagDocumentAnnotationParameters(
            documentId: "invalid-document-id",
            status: "Active",
            annotation: $annotation
        );
        $reqParams = $parameters->getRequestParameters();
        self::assertEquals("invalid-document-id", $parameters->documentId);
        self::assertEquals("Active", $reqParams['status']);
        self::assertEquals($annotation->toArray(), $reqParams['annotation']);
    }

    public function testRagDocumentsPostMustHaveValidProperties(): void
    {
        $response = $this->getResponse("post_response.json");
        self::assertNotNull($response);
        self::assertEquals("cc831599-c545-48b7-aa27-6d7ccd5b8d32", $response->id);
        self::assertEquals("Processing", $response->status);
        self::assertNull($response->annotation);
    }

    public function testRagDocumentsGetDraftMustHaveValidProperties(): void
    {
        $response = $this->getResponse("get_response_draft.json");
        self::assertNotNull($response);
        self::assertEquals("cc831599-c545-48b7-aa27-6d7ccd5b8d32", $response->id);
        self::assertEquals("Draft", $response->status);
        self::assertNotNull($response->annotation);

        $fields = $response->annotation->fields;
        self::assertNotNull($fields);

        // null simple field
        $tipField = $fields->getSimpleField('tip');
        self::assertFalse($tipField->selected);
        self::assertNull($tipField->guidelines);
        self::assertNull($tipField->value);

        // filled simple field
        $dateField = $fields->getSimpleField('date');
        self::assertFalse($dateField->selected);
        self::assertNull($dateField->guidelines);
        self::assertEquals("2019-11-02", $dateField->value);

        // filled object field
        $localeField = $fields->getObjectField('locale');
        self::assertFalse($localeField->selected);
        self::assertNull($localeField->guidelines);
        self::assertNotNull($localeField->fields);
        self::assertCount(3, $localeField->fields);
        self::assertEquals("US", $localeField->getSimpleField('country')->value);
        self::assertEquals("USD", $localeField->getSimpleField('currency')->value);
        self::assertNull($localeField->getSimpleField('language')->value);

        // list of simple fields
        $referenceNumbersField = $fields->getListField('reference_numbers');
        self::assertFalse($referenceNumbersField->selected);
        self::assertNull($referenceNumbersField->guidelines);
        self::assertCount(1, $referenceNumbersField->getSimpleItems());
        self::assertEquals("2412/2019", $referenceNumbersField->getSimpleItems()[0]->value);

        // list of object fields
        $lineItemsField = $fields->getListField('line_items');
        self::assertFalse($lineItemsField->selected);
        self::assertNull($lineItemsField->guidelines);
        self::assertCount(3, $lineItemsField->getObjectItems());

        $lineItem0 = $lineItemsField->getObjectItems()[0];
        self::assertNotNull($lineItem0->fields);
        self::assertCount(8, $lineItem0->fields);
        self::assertEquals("Front and rear brake cables", $lineItem0->getSimpleField('description')->value);
        self::assertEquals(1.0, $lineItem0->getSimpleField('quantity')->value);
        self::assertEquals(100.0, $lineItem0->getSimpleField('unit_price')->value);
        self::assertEquals(100.0, $lineItem0->getSimpleField('total_price')->value);
        self::assertNull($lineItem0->getSimpleField('tax_rate')->value);
        self::assertNull($lineItem0->getSimpleField('tax_amount')->value);
        self::assertNull($lineItem0->getSimpleField('product_code')->value);
        self::assertNull($lineItem0->getSimpleField('unit_measure')->value);

        $lineItem1 = $lineItemsField->getObjectItems()[1];
        self::assertNotNull($lineItem1->fields);
        self::assertCount(8, $lineItem1->fields);
        self::assertEquals("New set of pedal arms", $lineItem1->getSimpleField('description')->value);
        self::assertEquals(2.0, $lineItem1->getSimpleField('quantity')->value);
        self::assertEquals(25.0, $lineItem1->getSimpleField('unit_price')->value);
        self::assertEquals(50.0, $lineItem1->getSimpleField('total_price')->value);
        self::assertNull($lineItem1->getSimpleField('tax_rate')->value);
        self::assertNull($lineItem1->getSimpleField('tax_amount')->value);
        self::assertNull($lineItem1->getSimpleField('product_code')->value);
        self::assertNull($lineItem1->getSimpleField('unit_measure')->value);

        $lineItem2 = $lineItemsField->getObjectItems()[2];
        self::assertNotNull($lineItem2->fields);
        self::assertCount(8, $lineItem2->fields);
        self::assertEquals("Labor 3hrs", $lineItem2->getSimpleField('description')->value);
        self::assertEquals(3.0, $lineItem2->getSimpleField('quantity')->value);
        self::assertEquals(15.0, $lineItem2->getSimpleField('unit_price')->value);
        self::assertEquals(45.0, $lineItem2->getSimpleField('total_price')->value);
        self::assertNull($lineItem2->getSimpleField('tax_rate')->value);
        self::assertNull($lineItem2->getSimpleField('tax_amount')->value);
        self::assertNull($lineItem2->getSimpleField('product_code')->value);
        self::assertNull($lineItem2->getSimpleField('unit_measure')->value);
    }
}
