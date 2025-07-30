
<?php

use Mindee\Input\LocalResponse;
use Mindee\Parsing\V2\Field\ListField;
use Mindee\Parsing\V2\Field\ObjectField;
use Mindee\Parsing\V2\Field\SimpleField;
use Mindee\Parsing\V2\InferenceResponse;
use PHPUnit\Framework\TestCase;

/**
 * InferenceV2 – field integrity checks
 */
class InferenceTest extends TestCase
{
    private function loadFromResource(string $resourcePath): InferenceResponse
    {
        $fullPath = __DIR__ . '/../../resources/' . $resourcePath;
        $this->assertFileExists($fullPath, "Resource file must exist: $resourcePath");

        $localResponse = new LocalResponse($fullPath);
        return $localResponse->deserializeResponse(InferenceResponse::class);
    }

    private function readFileAsString(string $path): string
    {
        $fullPath = __DIR__ . '/../../resources/' . $path;
        $this->assertFileExists($fullPath, "Resource file must exist: $path");

        return file_get_contents($fullPath);
    }

    /**
     * When the async prediction is blank - all properties must be valid
     */
    public function testAsyncPredictWhenEmptyMustHaveValidProperties(): void
    {
        $response = $this->loadFromResource('v2/products/financial_document/blank.json');
        $fields = $response->inference->result->fields;

        $this->assertCount(21, $fields, 'Expected 21 fields');

        $taxes = $fields->get('taxes');
        $this->assertNotNull($taxes, "'taxes' field must exist");
        $this->assertInstanceOf(ListField::class, $taxes, "'taxes' must be a ListField");
        $this->assertEmpty($taxes->items, "'taxes' list must be empty");

        $supplierAddress = $fields->get('supplier_address');
        $this->assertNotNull($supplierAddress, "'supplier_address' field must exist");
        $this->assertInstanceOf(ObjectField::class, $supplierAddress, "'supplier_address' must be an ObjectField");

        foreach ($fields as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($value instanceof ListField) {
                $this->assertInstanceOf(ListField::class, $value, "$key – ListField expected");
            } elseif ($value instanceof ObjectField) {
                $this->assertInstanceOf(ObjectField::class, $value, "$key – ObjectField expected");
            } else {
                $this->assertInstanceOf(SimpleField::class, $value, "$key – SimpleField expected");
            }
        }
    }

    /**
     * When the async prediction is complete - every exposed property must be valid and consistent
     */
    public function testAsyncPredictWhenCompleteMustExposeAllProperties(): void
    {
        $response = $this->loadFromResource('v2/products/financial_document/complete.json');
        $inf = $response->inference;

        $this->assertNotNull($inf, 'Inference must not be null');
        $this->assertEquals('12345678-1234-1234-1234-123456789abc', $inf->id, 'Inference ID mismatch');

        $model = $inf->model;
        $this->assertNotNull($model, 'Model must not be null');
        $this->assertEquals('12345678-1234-1234-1234-123456789abc', $model->id, 'Model ID mismatch');

        $file = $inf->file;
        $this->assertNotNull($file, 'File must not be null');
        $this->assertEquals('complete.jpg', $file->name, 'File name mismatch');
        $this->assertEquals(1, $file->pageCount, 'File page count mismatch');
        $this->assertEquals('image/jpeg', $file->mimeType, 'File MIME type mismatch');
        $this->assertNull($file->alias ?? null, 'File alias must be null for this payload');

        $fields = $inf->result->fields;
        $this->assertCount(21, $fields, 'Expected 21 fields in the payload');

        $date = $fields->get('date');
        $this->assertInstanceOf(SimpleField::class, $date);
        $this->assertEquals('2019-11-02', $date->value, "'date' value mismatch");

        $taxes = $fields->get('taxes');
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

        $supplierAddress = $fields->get('supplier_address');
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

        $this->assertNull($inf->result->options ?? null, 'Options must be null');
    }

    /**
     * Deep nested fields - all nested structures must be typed correctly
     */
    public function testDeepNestedFieldsMustExposeCorrectTypes(): void
    {
        $resp = $this->loadFromResource('v2/inference/deep_nested_fields.json');
        $inf = $resp->inference;
        $this->assertNotNull($inf);

        $root = $inf->result->fields;
        $this->assertInstanceOf(SimpleField::class, $root->get('field_simple'));
        $this->assertInstanceOf(ObjectField::class, $root->get('field_object'));

        $fieldObject = $root->get('field_object');
        $this->assertInstanceOf(ObjectField::class, $fieldObject);
        $lvl1 = $fieldObject->fields;
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
        $response = $this->loadFromResource('v2/inference/standard_field_types.json');
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
        $this->assertIsNumeric($fieldSimpleInt->value);

        $fieldSimpleZero = $fields->get('field_simple_zero');
        $this->assertInstanceOf(SimpleField::class, $fieldSimpleZero);
        $this->assertIsNumeric($fieldSimpleZero->value);

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

        $subfield1 = $fieldObjectFields->get('subfield_1');
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
        $resp = $this->loadFromResource('v2/inference/raw_texts.json');
        $inf = $resp->inference;
        $this->assertNotNull($inf);

        $opts = $inf->result->options ?? null;
        $this->assertNotNull($opts, 'Options should not be null');

        $rawTexts = $opts->rawTexts ?? null;
        $this->assertNotNull($rawTexts, 'Raw texts should not be null');
        $this->assertCount(2, $rawTexts);

        $first = $rawTexts[0];
        $this->assertEquals(0, $first->page);
        $this->assertEquals('This is the raw text of the first page...', $first->content);
    }

    /**
     * RST display must be parsed and exposed
     */
    public function testRstDisplayMustBeAccessible(): void
    {
        $resp = $this->loadFromResource('v2/inference/standard_field_types.json');
        $rstRef = $this->readFileAsString('v2/inference/standard_field_types.rst');
        $inf = $resp->inference;
        $this->assertNotNull($inf);
        $this->assertEquals($rstRef, strval($resp->inference));
    }
}