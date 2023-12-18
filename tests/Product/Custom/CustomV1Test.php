<?php

namespace Product\Custom;

use Mindee\Geometry\Polygon;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Custom\ClassificationField;
use Mindee\Parsing\Custom\ListField;
use Mindee\Parsing\Custom\ListFieldValue;
use Mindee\Product\Custom\CustomV1;
use Mindee\Product\Custom\CustomV1Document;
use PHPUnit\Framework\TestCase;
use Product\ProductSharedData;

require_once(__DIR__."/../ProductSharedData.php");

class CustomV1Test extends TestCase
{
    public Document $customV1CompleteDoc;
    public Document $customV1EmptyDoc;

    protected function setUp(): void
    {
        $jsonV1Complete = file_get_contents(
            ProductSharedData::getProductDataDir() . "custom/response_v1/complete.json"
        );
        $rawDataV1Complete = json_decode($jsonV1Complete, true);

        $this->customV1CompleteDoc = new Document(CustomV1::class, $rawDataV1Complete["document"]);

        $jsonV1Empty = file_get_contents(
            ProductSharedData::getProductDataDir() . "custom/response_v1/empty.json"
        );
        $rawDataV1Empty = json_decode($jsonV1Empty, true);
        $this->customV1EmptyDoc = new Document(CustomV1::class, $rawDataV1Empty["document"]);
    }

    public function testEmptyDoc(): void
    {
        $documentPrediction = $this->customV1EmptyDoc->inference->prediction;
        assert($documentPrediction instanceof CustomV1Document);
        foreach ($documentPrediction->fields as $fieldName => $field) {
            $this->assertGreaterThan(0, strlen($fieldName));
            $this->assertInstanceOf(ListField::class, $field);
            $this->assertEquals(0, count($field->values));
        }
        foreach ($documentPrediction->classifications as $classificationName => $classification) {
            $this->assertGreaterThan(0, strlen($classificationName));
            $this->assertInstanceOf(ClassificationField::class, $classification);
            $this->assertNull($classification->value);
        }
    }

    public function testCompleteDoc()
    {
        $documentPrediction = $this->customV1CompleteDoc->inference->prediction;
        assert($documentPrediction instanceof CustomV1Document);
        $docStr = file_get_contents(ProductSharedData::getProductDataDir() . "custom/response_v1/summary_full.rst");
        foreach ($documentPrediction->fields as $fieldName => $field) {
            $this->assertGreaterThan(0, strlen($fieldName));
            $this->assertInstanceOf(ListField::class, $field);
            $this->assertGreaterThan(0, count($field->values));
            $this->assertEquals(count($field->values), count($field->contentsList()));
            foreach ($field->values as $value) {
                $this->assertInstanceOf(ListFieldValue::class, $value);
                $this->assertNotEquals("", $value->content);
                {
                    $this->assertInstanceOf(Polygon::class, $value->boundingBox);
                    $this->assertEquals(4, count($value->boundingBox->getCoordinates()));
                    $this->assertNotEquals(0.0, $value->confidence);
                }
            }
        }
        foreach ($documentPrediction->classifications as $classificationName => $classification) {
            $this->assertNotEquals(0, strlen($classificationName));
            $this->assertInstanceOf(ClassificationField::class, $classification);
            $this->assertNotEquals("", $classification->value);
        }
        $this->assertEquals(strval($this->customV1CompleteDoc), $docStr);
    }
}
