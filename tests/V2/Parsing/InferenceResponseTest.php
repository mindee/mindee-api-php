<?php

namespace V2\parsing;

use Mindee\Error\ErrorItem;
use Mindee\Geometry\Point;
use Mindee\Input\LocalResponse;
use Mindee\Parsing\V2\ErrorResponse;
use Mindee\Parsing\V2\Field\FieldConfidence;
use Mindee\Parsing\V2\Field\ListField;
use Mindee\Parsing\V2\Field\ObjectField;
use Mindee\Parsing\V2\Field\SimpleField;
use Mindee\Parsing\V2\InferenceResponse;
use Mindee\Parsing\V2\JobResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

require_once(__DIR__ . "/../../TestingUtilities.php");

/**
 * InferenceV2 – field integrity checks
 */
class InferenceResponseTest extends TestCase
{
    private function loadFromResource(string $resourcePath): InferenceResponse
    {
        $fullPath = TestingUtilities::getRootDataDir() . "/$resourcePath";
        $this->assertFileExists($fullPath, "Resource file must exist: $resourcePath");

        $localResponse = new LocalResponse($fullPath);
        return $localResponse->deserializeResponse(InferenceResponse::class);
    }

    private function readFileAsString(string $path): string
    {
        $this->assertFileExists($path, "Resource file must exist: $path");

        return file_get_contents($path);
    }

    /**
     * When the async prediction is blank - all properties must be valid
     */
    public function testAsyncPredictWhenEmptyMustHaveValidProperties(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/financial_document/blank.json');
        $fields = $response->inference->result->fields;

        $this->assertCount(21, $fields, 'Expected 21 fields');

        $this->assertInstanceOf(
            SimpleField::class,
            $fields['total_amount'],
            "Field 'total_amount' must be a SimpleField"
        );
        $totalAmount = $fields->getSimpleField('total_amount');
        $this->assertEmpty($totalAmount->value);

        $this->assertInstanceOf(
            ListField::class,
            $fields['taxes'],
            "Field 'taxes' must be a ListField"
        );
        $taxes = $fields->getListField('taxes');
        $this->assertEmpty($taxes->items);

        $this->assertInstanceOf(
            ObjectField::class,
            $fields['supplier_address'],
            "Field 'supplier_address' must be an ObjectField"
        );
        $supplierAddress = $fields->getObjectField('supplier_address');
        $this->assertCount(9, $supplierAddress->fields);

        foreach ($fields as $fieldName => $field) {
            if ($field === null) {
                continue;
            }
            if ($field instanceof ListField) {
                $this->assertEmpty($field->items, "Field $fieldName.items must be empty");
            } elseif ($field instanceof ObjectField) {
                foreach ($field->fields as $subFieldName => $subField) {
                    $this->assertEmpty($subField->value, "Field $fieldName.$subFieldName must be empty");
                }
            } elseif ($field instanceof SimpleField) {
                $this->assertIsNotObject($field->value, "Field $fieldName must be a scalar value");
            } else {
                $this->fail("Unknown field type: $fieldName");
            }
        }
    }

    /**
     * When the async prediction is complete - every exposed property must be valid and consistent
     */
    public function testAsyncPredictWhenCompleteMustExposeAllProperties(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/financial_document/complete.json');
        $inference = $response->inference;

        $this->assertNotNull($inference, 'Inference must not be null');
        $this->assertEquals('12345678-1234-1234-1234-123456789abc', $inference->id, 'Inference ID mismatch');

        $model = $inference->model;
        $this->assertNotNull($model, 'Model must not be null');
        $this->assertEquals('12345678-1234-1234-1234-123456789abc', $model->id, 'Model ID mismatch');

        $file = $inference->file;
        $this->assertNotNull($file, 'File must not be null');
        $this->assertEquals('complete.jpg', $file->name, 'File name mismatch');
        $this->assertEquals(1, $file->pageCount, 'File page count mismatch');
        $this->assertEquals('image/jpeg', $file->mimeType, 'File MIME type mismatch');
        $this->assertNull($file->alias ?? null, 'File alias must be null for this payload');

        $fields = $inference->result->fields;
        $this->assertCount(21, $fields, 'Expected 21 fields in the payload');

        $date = $fields->get('date');
        $this->assertInstanceOf(SimpleField::class, $date);
        $this->assertEquals('2019-11-02', $date->value, "'date' value mismatch");

        $taxes = $fields->getListField('taxes');
        $this->assertNotNull($taxes, "'taxes' field must exist");
        $this->assertInstanceOf(ListField::class, $taxes, "'taxes' must be a ListField");
        $this->assertCount(1, $taxes->items, "'taxes' list must contain exactly one item");

        $taxItemObj = $taxes->items[0];
        $this->assertInstanceOf(ObjectField::class, $taxItemObj, 'First item of "taxes" must be an ObjectField');
        $this->assertCount(3, $taxItemObj->fields, 'Tax ObjectField must contain 3 sub-fields');

        $baseTax = $taxItemObj->fields->get('base');
        $this->assertInstanceOf(SimpleField::class, $baseTax);
        $this->assertEquals(31.5, $baseTax->value, "'taxes.base' value mismatch");
        $this->assertNotNull(strval($taxes), "'taxes'.__toString() must not be null");

        $supplierAddress = $fields->getObjectField('supplier_address');
        $this->assertNotNull($supplierAddress, "'supplier_address' field must exist");
        $this->assertInstanceOf(ObjectField::class, $supplierAddress, "'supplier_address' must be an ObjectField");

        $country = $supplierAddress->fields->get('country');
        $this->assertNotNull($country, "'supplier_address.country' must exist");
        $this->assertInstanceOf(SimpleField::class, $country);
        $this->assertEquals('USA', $country->value, 'Country mismatch');
        $this->assertEquals('USA', strval($country), "'country'.__toString() mismatch");
        $this->assertNotNull(strval($supplierAddress), "'supplier_address'.__toString() must not be null");

        $customerAddr = $fields->get('customer_address');
        $this->assertInstanceOf(ObjectField::class, $customerAddr);
        $city = $customerAddr->fields->get('city');
        $this->assertInstanceOf(SimpleField::class, $city);
        $this->assertEquals('New York', $city->value, 'City mismatch');

        $this->assertNull($inference->result->options ?? null, 'Options must be null');
    }

    /**
     * Deep nested fields - all nested structures must be typed correctly
     */
    public function testDeepNestedFieldsMustExposeCorrectTypes(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/deep_nested_fields.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $root = $inference->result->fields;
        $this->assertInstanceOf(SimpleField::class, $root->get('field_simple'));
        $this->assertInstanceOf(ObjectField::class, $root->get('field_object'));

        $fieldObject = $root->get('field_object');
        $this->assertInstanceOf(ObjectField::class, $fieldObject);
        $this->assertInstanceOf(SimpleField::class, $fieldObject->getSimpleField('sub_object_simple'));
        $this->assertInstanceOf(ListField::class, $fieldObject->getListField('sub_object_list'));
        $this->assertInstanceOf(ObjectField::class, $fieldObject->getObjectField('sub_object_object'));
        $this->assertEquals(1, count($fieldObject->getSimpleFields()));
        $this->assertEquals(1, count($fieldObject->getListFields()));
        $this->assertEquals(1, count($fieldObject->getObjectFields()));
        $lvl1 = $fieldObject->fields;
        $this->assertInstanceOf(SimpleField::class, $lvl1->get('sub_object_simple'));
        $this->assertInstanceOf(ListField::class, $lvl1->get('sub_object_list'));
        $this->assertInstanceOf(ObjectField::class, $lvl1->get('sub_object_object'));

        $subObjectObject = $lvl1->get('sub_object_object');
        $this->assertInstanceOf(ObjectField::class, $subObjectObject);
        $lvl2 = $subObjectObject->fields;
        $this->assertInstanceOf(ListField::class, $lvl2->get('sub_object_object_sub_object_list'));

        $nestedList = $lvl2->get('sub_object_object_sub_object_list');
        $this->assertInstanceOf(ListField::class, $nestedList);
        $items = $nestedList->items;
        $this->assertNotEmpty($items);
        $this->assertInstanceOf(ObjectField::class, $items[0]);

        $firstItem = $items[0];
        $this->assertInstanceOf(ObjectField::class, $firstItem);
        $deepSimple = $firstItem->fields->get('sub_object_object_sub_object_list_simple');
        $this->assertInstanceOf(SimpleField::class, $deepSimple);
        $this->assertEquals('value_9', $deepSimple->value);
    }

    /**
     * Standard field types - simple / object / list variants must be recognised
     */
    public function testStandardFieldTypesMustExposeCorrectTypes(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/standard_field_types.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $fields = $inference->result->fields;

        $fieldSimpleString = $fields->get('field_simple_string');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleString);
        $this->assertIsString($fieldSimpleString->value);

        $fieldSimpleFloat = $fields->get('field_simple_float');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleFloat);
        $this->assertIsFloat($fieldSimpleFloat->value);

        $fieldSimpleInt = $fields->get('field_simple_int');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleInt);
        $this->assertIsFloat($fieldSimpleInt->value);

        $fieldSimpleZero = $fields->get('field_simple_zero');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleZero);
        $this->assertIsFloat($fieldSimpleZero->value);

        $fieldSimpleBool = $fields->get('field_simple_bool');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleBool);
        $this->assertIsBool($fieldSimpleBool->value);

        $fieldSimpleNull = $fields->get('field_simple_null');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleNull);
        $this->assertNull($fieldSimpleNull->value);

        $fieldSimpleList = $fields->get('field_simple_list');
        $this->assertInstanceOf(ListField::class, $fieldSimpleList);
        $simpleItems = $fieldSimpleList->items;
        $this->assertCount(2, $simpleItems);

        $firstSimpleItem = $simpleItems[0];
        $this->assertInstanceOf(SimpleField::class, $firstSimpleItem);
        $this->assertIsString($firstSimpleItem->value);

        foreach ($fieldSimpleList->items as $item) {
            $this->assertInstanceOf(SimpleField::class, $item);
            $this->assertIsString($item->value);
        }

        $fieldObject = $fields->get('field_object');
        $this->assertInstanceOf(ObjectField::class, $fieldObject);
        $fieldObjectFields = $fieldObject->fields;
        $this->assertCount(2, $fieldObjectFields);
        foreach ($fieldObjectFields as $fieldName => $subField) {
            $this->assertInstanceOf(SimpleField::class, $subField);
        }

        $subfield1 = $fieldObjectFields->getSimpleField('subfield_1');
        $this->assertInstanceOf(SimpleField::class, $subfield1);
        $this->assertIsString($subfield1->value);

        $fieldObjectList = $fields->get('field_object_list');
        $this->assertInstanceOf(ListField::class, $fieldObjectList);
        $objectItems = $fieldObjectList->items;
        $this->assertCount(2, $objectItems);

        $firstObjectItem = $objectItems[0];
        $this->assertInstanceOf(ObjectField::class, $firstObjectItem);

        $firstObjectSubfield = $firstObjectItem->fields->get('subfield_1');
        $this->assertInstanceOf(SimpleField::class, $firstObjectSubfield);
        $this->assertIsString($firstObjectSubfield->value);

        foreach ($fieldObjectList->items as $item) {
            $this->assertInstanceOf(ObjectField::class, $item);
            $subfield = $item->fields->get('subfield_1');
            $this->assertInstanceOf(SimpleField::class, $subfield);
            $this->assertIsString($subfield->value);
        }
    }

    /**
     * Raw texts option must be parsed and exposed
     */
    public function testRawTextsMustBeAccessible(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/raw_texts.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $activeOptions = $inference->activeOptions;
        $this->assertTrue($activeOptions->rawText);
        $this->assertFalse($activeOptions->polygon);
        $this->assertFalse($activeOptions->confidence);
        $this->assertFalse($activeOptions->rag);

        $rawText = $inference->result->rawText;
        $this->assertNotNull($rawText);
        $this->assertCount(2, $rawText->pages);

        $first = $rawText->pages[0];
        $this->assertEquals('This is the raw text of the first page...', $first->content);

        foreach ($rawText->pages as $page) {
            $this->assertIsString($page->content);
        }
    }

    /**
     * RST display must be parsed and exposed
     */
    public function testRstDisplayMustBeAccessible(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/standard_field_types.json');
        $expectedRst = $this->readFileAsString(
            \TestingUtilities::getV2DataDir() . '/products/extraction/standard_field_types.rst'
        );
        $inference = $response->inference;
        $this->assertNotNull($inference);
        $this->assertEquals($expectedRst, strval($response->inference));
    }

    /**
     * Coordinates & location data must be parsed and exposed.
     */
    public function testCoordinatesAndLocationDataMustBeAccessible(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/financial_document/complete_with_coordinates.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);

        $fields = $response->inference->result->fields;

        $dateField = $fields->getSimpleField('date');
        $this->assertCount(1, $dateField->locations);

        $location = $dateField->locations[0];
        $this->assertNotNull($location);
        $this->assertEquals(0, $location->page);
        $this->assertEquals(
            0.948979073166918,
            $location->polygon->coordinates[0]->getX()
        );
        $this->assertEquals(
            0.23097924535067715,
            $location->polygon->coordinates[0]->getY()
        );
        $this->assertEquals(0.85422, $location->polygon->coordinates[1][0]);
        $this->assertEquals(0.230072, $location->polygon->coordinates[1][1]);
        $this->assertEquals(
            0.8540899268330819,
            $location->polygon->coordinates[2][0]
        );
        $this->assertEquals(
            0.24365775464932288,
            $location->polygon->coordinates[2][1]
        );
        $this->assertEquals(0.948849, $location->polygon->coordinates[3][0]);
        $this->assertEquals(0.244565, $location->polygon->coordinates[3][1]);
        $this->assertEquals(
            new Point(0.9015345, 0.23731850000000002),
            $location->polygon->getCentroid()
        );
        $this->assertEquals(FieldConfidence::Medium, $dateField->confidence);
        $this->assertEquals(FieldConfidence::Medium->rank(), $dateField->confidence->rank());
        $this->assertTrue(FieldConfidence::Medium->equal($dateField->confidence));
        $this->assertLessThan(FieldConfidence::High->rank(), $dateField->confidence->rank());
        $this->assertTrue(FieldConfidence::High->greaterThan($dateField->confidence));
        $this->assertTrue(FieldConfidence::Medium->greaterThanOrEqual($dateField->confidence));
        $this->assertTrue(FieldConfidence::High->greaterThanOrEqual($dateField->confidence));
        $this->assertGreaterThan(FieldConfidence::Low->rank(), $dateField->confidence->rank());
        $this->assertTrue(FieldConfidence::Low->lessThan($dateField->confidence));
        $this->assertTrue(FieldConfidence::Low->lessThanOrEqual($dateField->confidence));
        $this->assertTrue(FieldConfidence::Medium->lessThanOrEqual($dateField->confidence));
        $this->assertEquals('Medium', $dateField->confidence->value);

        $activeOptions = $inference->activeOptions;
        $this->assertTrue($activeOptions->polygon);
        $this->assertFalse($activeOptions->confidence);
        $this->assertFalse($activeOptions->rag);
        $this->assertFalse($activeOptions->rawText);
        $this->assertFalse($activeOptions->textContext);
    }

    public function testRagMetadataWhenMatched()
    {
        $response = $this->loadFromResource('v2/products/extraction/rag_matched.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);
        $this->assertEquals('12345abc-1234-1234-1234-123456789abc', $inference->result->rag->retrievedDocumentId);
    }

    public function testRagMetadataWhenNotMatched()
    {
        $response = $this->loadFromResource('v2/products/extraction/rag_not_matched.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);
        $this->assertNull($inference->result->rag->retrievedDocumentId);
    }

    public function testShouldLoadWith422Error()
    {
        $jsonResponse = json_decode(file_get_contents(\TestingUtilities::getV2DataDir() . '/job/fail_422.json'), true);
        $response = new JobResponse($jsonResponse);
        $this->assertNotNull($response->job);
        $this->assertInstanceOf(ErrorResponse::class, $response->job->error);
        $this->assertEquals(422, $response->job->error->status);
        $this->assertStringStartsWith("422-", $response->job->error->code);
        $this->assertEquals(1, count($response->job->error->errors));
        $this->assertInstanceOf(ErrorItem::class, $response->job->error->errors[0]);
    }

    public function testTextContextIsTrue(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/text_context_enabled.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);
        $activeOptions = $inference->activeOptions;
        $this->assertFalse($activeOptions->polygon);
        $this->assertFalse($activeOptions->confidence);
        $this->assertFalse($activeOptions->rag);
        $this->assertFalse($activeOptions->rawText);
        $this->assertTrue($activeOptions->textContext);
    }

    public function testTextContextIsFalse(): void
    {
        $response = $this->loadFromResource('v2/products/extraction/financial_document/complete.json');
        $inference = $response->inference;
        $this->assertNotNull($inference);
        $activeOptions = $inference->activeOptions;
        $this->assertFalse($activeOptions->polygon);
        $this->assertFalse($activeOptions->confidence);
        $this->assertFalse($activeOptions->rag);
        $this->assertFalse($activeOptions->rawText);
        $this->assertFalse($activeOptions->textContext);
    }
}
