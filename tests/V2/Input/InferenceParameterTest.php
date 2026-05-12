<?php

declare(strict_types=1);

namespace V2\Input;

use Mindee\V2\Product\Extraction\Params\DataSchema;
use Mindee\V2\Product\Extraction\Params\ExtractionParameters;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class InferenceParameterTest extends TestCase
{
    private array $expectedSchemaDict;
    private string $expectedSchemaString;
    private DataSchema $expectedSchemaObject;

    protected function setUp(): void
    {
        $fileContents = file_get_contents(TestingUtilities::getV2DataDir() . '/products/extraction/data_schema_replace_param.json');
        $this->expectedSchemaString = $fileContents;
        $this->expectedSchemaDict = json_decode($fileContents, true);
        $this->expectedSchemaObject = new DataSchema($fileContents);
    }

    public function testDataSchemaShouldntReplaceWhenUnset(): void
    {
        $params = new ExtractionParameters('model_id', dataSchema: null);
        self::assertFalse(isset($params->dataSchema));
    }

    public function testDataSchemaShouldEquateNoMatterTheType(): void
    {
        $paramsDict = new ExtractionParameters('model_id', dataSchema: $this->expectedSchemaDict);
        $paramsString = new ExtractionParameters('model_id', dataSchema: $this->expectedSchemaString);
        $paramsObject = new ExtractionParameters('model_id', dataSchema: $this->expectedSchemaObject);
        self::assertSame((string) ($paramsDict->dataSchema), $this->expectedSchemaString);
        self::assertSame((string) ($paramsObject->dataSchema), $this->expectedSchemaString);
        self::assertSame((string) ($paramsString->dataSchema), $this->expectedSchemaString);
    }
}
