<?php

namespace V2\Input;


use Mindee\Input\DataSchema;
use Mindee\Input\InferenceParameters;
use PHPUnit\Framework\TestCase;

class InferenceParameterTest extends TestCase
{
    private array $expectedSchemaDict;
    private string $expectedSchemaString;
    private DataSchema $expectedSchemaObject;

    protected function setUp(): void {
        $fileContents = file_get_contents(\TestingUtilities::getV2DataDir() . '/products/extraction/data_schema_replace_param.json');
        $this->expectedSchemaString = $fileContents;
        $this->expectedSchemaDict = json_decode($fileContents, true);
        $this->expectedSchemaObject = new DataSchema($fileContents);
    }

    public function testDataSchemaShouldntReplaceWhenUnset() {
        $params = new InferenceParameters('model_id', dataSchema: null);
        $this->assertFalse(isset($params->dataSchema));
    }

    public function testDataSchemaShouldEquateNoMatterTheType(){
        $paramsDict = new InferenceParameters('model_id', dataSchema: $this->expectedSchemaDict);
        $paramsString = new InferenceParameters('model_id', dataSchema: $this->expectedSchemaString);
        $paramsObject = new InferenceParameters('model_id', dataSchema: $this->expectedSchemaObject);
        $this->assertEquals(strval($paramsDict->dataSchema), $this->expectedSchemaString);
        $this->assertEquals(strval($paramsObject->dataSchema), $this->expectedSchemaString);
        $this->assertEquals(strval($paramsString->dataSchema), $this->expectedSchemaString);
    }
}
