<?php

declare(strict_types=1);

namespace V2\Parsing;

use Mindee\Geometry\Point;
use Mindee\Input\LocalResponse;
use Mindee\V2\Parsing\Error\ErrorItem;
use Mindee\V2\Parsing\Error\ErrorResponse;
use Mindee\V2\Parsing\Inference\Field\FieldConfidence;
use Mindee\V2\Parsing\Inference\Field\ListField;
use Mindee\V2\Parsing\Inference\Field\ObjectField;
use Mindee\V2\Parsing\Inference\Field\SimpleField;
use Mindee\V2\Parsing\Job\JobResponse;
use Mindee\V2\Product\Extraction\ExtractionResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * InferenceV2 – field integrity checks
 */
class ExtractionResponseTest extends TestCase
{
    private function loadFromResource(string $resourcePath): ExtractionResponse
    {
        $fullPath = TestingUtilities::getV2ProductDir() . "/$resourcePath";
        self::assertFileExists($fullPath, "Resource file must exist: $resourcePath");

        $localResponse = new LocalResponse($fullPath);
        return $localResponse->deserializeResponse(ExtractionResponse::class);
    }

    private function readFileAsString(string $path): string
    {
        self::assertFileExists($path, "Resource file must exist: $path");

        return file_get_contents($path);
    }

    /**
     * When the async prediction is blank - all properties must be valid.
     */
    public function testAsyncPredictWhenEmptyMustHaveValidProperties(): void
    {
        $response = $this->loadFromResource('extraction/financial_document/blank.json');
        $fields = $response->inference->result->fields;

        self::assertCount(21, $fields, 'Expected 21 fields');

        self::assertInstanceOf(
            SimpleField::class,
            $fields['total_amount'],
            "Field 'total_amount' must be a SimpleField"
        );
        $totalAmount = $fields->getSimpleField('total_amount');
        self::assertEmpty($totalAmount->value);

        self::assertInstanceOf(
            ListField::class,
            $fields['taxes'],
            "Field 'taxes' must be a ListField"
        );
        $taxes = $fields->getListField('taxes');
        self::assertEmpty($taxes->items);

        self::assertInstanceOf(
            ObjectField::class,
            $fields['supplier_address'],
            "Field 'supplier_address' must be an ObjectField"
        );
        $supplierAddress = $fields->getObjectField('supplier_address');
        self::assertCount(9, $supplierAddress->fields);

        foreach ($fields as $fieldName => $field) {
            if (null === $field) {
                continue;
            }
            if ($field instanceof ListField) {
                self::assertEmpty($field->items, "Field $fieldName.items must be empty");
            } elseif ($field instanceof ObjectField) {
                foreach ($field->fields as $subFieldName => $subField) {
                    self::assertEmpty($subField->value, "Field $fieldName.$subFieldName must be empty");
                }
            } elseif ($field instanceof SimpleField) {
                self::assertIsNotObject($field->value, "Field $fieldName must be a scalar value");
            } else {
                self::fail("Unknown field type: $fieldName");
            }
        }
    }

    /**
     * When the async prediction is complete - every exposed property must be valid and consistent.
     */
    public function testAsyncPredictWhenCompleteMustExposeAllProperties(): void
    {
        $response = $this->loadFromResource('extraction/financial_document/complete.json');
        $inference = $response->inference;

        self::assertNotNull($inference, 'Inference must not be null');
        self::assertSame('12345678-1234-1234-1234-123456789abc', $inference->id, 'ExtractionInference ID mismatch');

        $model = $inference->model;
        self::assertNotNull($model, 'Model must not be null');
        self::assertSame('12345678-1234-1234-1234-123456789abc', $model->id, 'Model ID mismatch');

        $file = $inference->file;
        self::assertNotNull($file, 'File must not be null');
        self::assertSame('complete.jpg', $file->name, 'File name mismatch');
        self::assertSame(1, $file->pageCount, 'File page count mismatch');
        self::assertSame('image/jpeg', $file->mimeType, 'File MIME type mismatch');
        self::assertNull($file->alias ?? null, 'File alias must be null for this payload');

        $fields = $inference->result->fields;
        self::assertCount(21, $fields, 'Expected 21 fields in the payload');

        $date = $fields->get('date');
        self::assertInstanceOf(SimpleField::class, $date);
        self::assertSame('2019-11-02', $date->value, "'date' value mismatch");

        $taxes = $fields->getListField('taxes');
        self::assertNotNull($taxes, "'taxes' field must exist");
        self::assertInstanceOf(ListField::class, $taxes, "'taxes' must be a ListField");
        self::assertCount(1, $taxes->items, "'taxes' list must contain exactly one item");

        $taxItemObj = $taxes->items[0];
        self::assertInstanceOf(ObjectField::class, $taxItemObj, 'First item of "taxes" must be an ObjectField');
        self::assertCount(3, $taxItemObj->fields, 'Tax ObjectField must contain 3 sub-fields');

        $baseTax = $taxItemObj->fields->get('base');
        self::assertInstanceOf(SimpleField::class, $baseTax);
        self::assertSame(31.5, $baseTax->value, "'taxes.base' value mismatch");
        self::assertNotNull((string) $taxes, "'taxes'.__toString() must not be null");

        $supplierAddress = $fields->getObjectField('supplier_address');
        self::assertNotNull($supplierAddress, "'supplier_address' field must exist");
        self::assertInstanceOf(ObjectField::class, $supplierAddress, "'supplier_address' must be an ObjectField");

        $country = $supplierAddress->fields->get('country');
        self::assertNotNull($country, "'supplier_address.country' must exist");
        self::assertInstanceOf(SimpleField::class, $country);
        self::assertSame('USA', $country->value, 'Country mismatch');
        self::assertSame('USA', (string) $country, "'country'.__toString() mismatch");
        self::assertNotNull((string) $supplierAddress, "'supplier_address'.__toString() must not be null");

        $customerAddr = $fields->get('customer_address');
        self::assertInstanceOf(ObjectField::class, $customerAddr);
        $city = $customerAddr->fields->get('city');
        self::assertInstanceOf(SimpleField::class, $city);
        self::assertSame('New York', $city->value, 'City mismatch');

        self::assertNull($inference->result->options ?? null, 'Options must be null');
    }

    /**
     * Deep nested fields - all nested structures must be typed correctly.
     */
    public function testDeepNestedFieldsMustExposeCorrectTypes(): void
    {
        $response = $this->loadFromResource('extraction/deep_nested_fields.json');
        $inference = $response->inference;
        self::assertNotNull($inference);

        $root = $inference->result->fields;
        self::assertInstanceOf(SimpleField::class, $root->get('field_simple'));
        self::assertInstanceOf(ObjectField::class, $root->get('field_object'));

        $fieldObject = $root->get('field_object');
        self::assertInstanceOf(ObjectField::class, $fieldObject);
        self::assertInstanceOf(SimpleField::class, $fieldObject->getSimpleField('sub_object_simple'));
        self::assertInstanceOf(ListField::class, $fieldObject->getListField('sub_object_list'));
        self::assertInstanceOf(ObjectField::class, $fieldObject->getObjectField('sub_object_object'));
        self::assertCount(1, $fieldObject->getSimpleFields());
        self::assertCount(1, $fieldObject->getListFields());
        self::assertCount(1, $fieldObject->getObjectFields());
        $lvl1 = $fieldObject->fields;
        self::assertInstanceOf(SimpleField::class, $lvl1->get('sub_object_simple'));
        self::assertInstanceOf(ListField::class, $lvl1->get('sub_object_list'));
        self::assertInstanceOf(ObjectField::class, $lvl1->get('sub_object_object'));

        $subObjectObject = $lvl1->get('sub_object_object');
        self::assertInstanceOf(ObjectField::class, $subObjectObject);
        $lvl2 = $subObjectObject->fields;
        self::assertInstanceOf(ListField::class, $lvl2->get('sub_object_object_sub_object_list'));

        $nestedList = $lvl2->get('sub_object_object_sub_object_list');
        self::assertInstanceOf(ListField::class, $nestedList);
        $items = $nestedList->items;
        self::assertNotEmpty($items);
        self::assertInstanceOf(ObjectField::class, $items[0]);

        $firstItem = $items[0];
        self::assertInstanceOf(ObjectField::class, $firstItem);
        $deepSimple = $firstItem->fields->get('sub_object_object_sub_object_list_simple');
        self::assertInstanceOf(SimpleField::class, $deepSimple);
        self::assertSame('value_9', $deepSimple->value);
    }

    /**
     * Standard field types - simple / object / list variants must be recognised.
     */
    public function testStandardFieldTypesMustExposeCorrectTypes(): void
    {
        $response = $this->loadFromResource('extraction/standard_field_types.json');
        $inference = $response->inference;
        self::assertNotNull($inference);

        $fields = $inference->result->fields;

        $fieldSimpleString = $fields->get('field_simple_string');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleString);
        self::assertIsString($fieldSimpleString->value);

        $fieldSimpleFloat = $fields->get('field_simple_float');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleFloat);
        self::assertIsFloat($fieldSimpleFloat->value);

        $fieldSimpleInt = $fields->get('field_simple_int');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleInt);
        self::assertIsFloat($fieldSimpleInt->value);

        $fieldSimpleZero = $fields->get('field_simple_zero');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleZero);
        self::assertIsFloat($fieldSimpleZero->value);

        $fieldSimpleBool = $fields->get('field_simple_bool');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleBool);
        self::assertIsBool($fieldSimpleBool->value);

        $fieldSimpleNull = $fields->get('field_simple_null');
        self::assertInstanceOf(SimpleField::class, $fieldSimpleNull);
        self::assertNull($fieldSimpleNull->value);

        $fieldSimpleList = $fields->get('field_simple_list');
        self::assertInstanceOf(ListField::class, $fieldSimpleList);
        $simpleItems = $fieldSimpleList->items;
        self::assertCount(2, $simpleItems);

        $firstSimpleItem = $simpleItems[0];
        self::assertInstanceOf(SimpleField::class, $firstSimpleItem);
        self::assertIsString($firstSimpleItem->value);

        foreach ($fieldSimpleList->items as $item) {
            self::assertInstanceOf(SimpleField::class, $item);
            self::assertIsString($item->value);
        }

        $fieldObject = $fields->get('field_object');
        self::assertInstanceOf(ObjectField::class, $fieldObject);
        $fieldObjectFields = $fieldObject->fields;
        self::assertCount(2, $fieldObjectFields);
        foreach ($fieldObjectFields as $subField) {
            self::assertInstanceOf(SimpleField::class, $subField);
        }

        $subfield1 = $fieldObjectFields->getSimpleField('subfield_1');
        self::assertInstanceOf(SimpleField::class, $subfield1);
        self::assertIsString($subfield1->value);

        $fieldObjectList = $fields->get('field_object_list');
        self::assertInstanceOf(ListField::class, $fieldObjectList);
        $objectItems = $fieldObjectList->items;
        self::assertCount(2, $objectItems);

        $firstObjectItem = $objectItems[0];
        self::assertInstanceOf(ObjectField::class, $firstObjectItem);

        $firstObjectSubfield = $firstObjectItem->fields->get('subfield_1');
        self::assertInstanceOf(SimpleField::class, $firstObjectSubfield);
        self::assertIsString($firstObjectSubfield->value);

        foreach ($fieldObjectList->items as $item) {
            self::assertInstanceOf(ObjectField::class, $item);
            $subfield = $item->fields->get('subfield_1');
            self::assertInstanceOf(SimpleField::class, $subfield);
            self::assertIsString($subfield->value);
        }
    }

    /**
     * Raw texts option must be parsed and exposed.
     */
    public function testRawTextsMustBeAccessible(): void
    {
        $response = $this->loadFromResource('extraction/raw_texts.json');
        $inference = $response->inference;
        self::assertNotNull($inference);

        $activeOptions = $inference->activeOptions;
        self::assertTrue($activeOptions->rawText);
        self::assertFalse($activeOptions->polygon);
        self::assertFalse($activeOptions->confidence);
        self::assertFalse($activeOptions->rag);

        $rawText = $inference->result->rawText;
        self::assertNotNull($rawText);
        self::assertCount(2, $rawText->pages);

        $first = $rawText->pages[0];
        self::assertSame('This is the raw text of the first page...', $first->content);

        foreach ($rawText->pages as $page) {
            self::assertIsString($page->content);
        }
    }

    /**
     * RST display must be parsed and exposed.
     */
    public function testRstDisplayMustBeAccessible(): void
    {
        $response = $this->loadFromResource('extraction/standard_field_types.json');
        $expectedRst = $this->readFileAsString(
            TestingUtilities::getV2ProductDir() . '/extraction/standard_field_types.rst'
        );
        $inference = $response->inference;
        self::assertNotNull($inference);
        self::assertSame($expectedRst, (string) ($response->inference));
    }

    /**
     * Coordinates & location data must be parsed and exposed.
     */
    public function testCoordinatesAndLocationDataMustBeAccessible(): void
    {
        $response = $this->loadFromResource('extraction/financial_document/complete_with_coordinates.json');
        $inference = $response->inference;
        self::assertNotNull($inference);

        $fields = $response->inference->result->fields;

        $dateField = $fields->getSimpleField('date');
        self::assertCount(1, $dateField->locations);

        $location = $dateField->locations[0];
        self::assertNotNull($location);
        self::assertSame(0, $location->page);
        self::assertSame(
            0.948979073166918,
            $location->polygon->coordinates[0]->getX()
        );
        self::assertSame(
            0.23097924535067715,
            $location->polygon->coordinates[0]->getY()
        );
        self::assertSame(0.85422, $location->polygon->coordinates[1][0]);
        self::assertSame(0.230072, $location->polygon->coordinates[1][1]);
        self::assertSame(
            0.8540899268330819,
            $location->polygon->coordinates[2][0]
        );
        self::assertSame(
            0.24365775464932288,
            $location->polygon->coordinates[2][1]
        );
        self::assertSame(0.948849, $location->polygon->coordinates[3][0]);
        self::assertSame(0.244565, $location->polygon->coordinates[3][1]);
        self::assertEquals(
            new Point(0.9015345, 0.23731850000000002),
            $location->polygon->getCentroid()
        );
        self::assertSame(FieldConfidence::Medium, $dateField->confidence);
        self::assertSame(FieldConfidence::Medium->rank(), $dateField->confidence->rank());
        self::assertTrue(FieldConfidence::Medium->equal($dateField->confidence));
        self::assertLessThan(FieldConfidence::High->rank(), $dateField->confidence->rank());
        self::assertTrue(FieldConfidence::High->greaterThan($dateField->confidence));
        self::assertTrue(FieldConfidence::Medium->greaterThanOrEqual($dateField->confidence));
        self::assertTrue(FieldConfidence::High->greaterThanOrEqual($dateField->confidence));
        self::assertGreaterThan(FieldConfidence::Low->rank(), $dateField->confidence->rank());
        self::assertTrue(FieldConfidence::Low->lessThan($dateField->confidence));
        self::assertTrue(FieldConfidence::Low->lessThanOrEqual($dateField->confidence));
        self::assertTrue(FieldConfidence::Medium->lessThanOrEqual($dateField->confidence));
        self::assertSame('Medium', $dateField->confidence->value);

        $activeOptions = $inference->activeOptions;
        self::assertTrue($activeOptions->polygon);
        self::assertFalse($activeOptions->confidence);
        self::assertFalse($activeOptions->rag);
        self::assertFalse($activeOptions->rawText);
        self::assertFalse($activeOptions->textContext);
    }

    public function testRagMetadataWhenMatched(): void
    {
        $response = $this->loadFromResource('extraction/rag_matched.json');
        $inference = $response->inference;
        self::assertNotNull($inference);
        self::assertSame('12345abc-1234-1234-1234-123456789abc', $inference->result->rag->retrievedDocumentId);
    }

    public function testRagMetadataWhenNotMatched(): void
    {
        $response = $this->loadFromResource('extraction/rag_not_matched.json');
        $inference = $response->inference;
        self::assertNotNull($inference);
        self::assertNull($inference->result->rag->retrievedDocumentId);
    }

    public function testShouldLoadWith422Error(): void
    {
        $jsonResponse = json_decode(
            file_get_contents(TestingUtilities::getV2DataDir() . '/job/fail_422.json'),
            true
        );
        $response = new JobResponse($jsonResponse);
        self::assertNotNull($response->job);
        self::assertInstanceOf(ErrorResponse::class, $response->job->error);
        self::assertSame(422, $response->job->error->status);
        self::assertStringStartsWith("422-", $response->job->error->code);
        self::assertCount(1, $response->job->error->errors);
        self::assertInstanceOf(ErrorItem::class, $response->job->error->errors[0]);
    }

    public function testTextContextIsTrue(): void
    {
        $response = $this->loadFromResource('extraction/text_context_enabled.json');
        $inference = $response->inference;
        self::assertNotNull($inference);
        $activeOptions = $inference->activeOptions;
        self::assertFalse($activeOptions->polygon);
        self::assertFalse($activeOptions->confidence);
        self::assertFalse($activeOptions->rag);
        self::assertFalse($activeOptions->rawText);
        self::assertTrue($activeOptions->textContext);
    }

    public function testTextContextIsFalse(): void
    {
        $response = $this->loadFromResource('extraction/financial_document/complete.json');
        $inference = $response->inference;
        self::assertNotNull($inference);
        $activeOptions = $inference->activeOptions;
        self::assertFalse($activeOptions->polygon);
        self::assertFalse($activeOptions->confidence);
        self::assertFalse($activeOptions->rag);
        self::assertFalse($activeOptions->rawText);
        self::assertFalse($activeOptions->textContext);
    }
}
