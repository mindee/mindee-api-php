<?php

namespace V1\Product\NutritionFactsLabel;

use Mindee\Parsing\Common\Document;
use Mindee\Product\NutritionFactsLabel;
use PHPUnit\Framework\TestCase;

class NutritionFactsLabelV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/nutrition_facts/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(NutritionFactsLabel\NutritionFactsLabelV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(NutritionFactsLabel\NutritionFactsLabelV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->servingPerBox->value);
        $this->assertNull($prediction->servingSize->amount);
        $this->assertNull($prediction->servingSize->unit);
        $this->assertNull($prediction->calories->dailyValue);
        $this->assertNull($prediction->calories->per100G);
        $this->assertNull($prediction->calories->perServing);
        $this->assertNull($prediction->totalFat->dailyValue);
        $this->assertNull($prediction->totalFat->per100G);
        $this->assertNull($prediction->totalFat->perServing);
        $this->assertNull($prediction->saturatedFat->dailyValue);
        $this->assertNull($prediction->saturatedFat->per100G);
        $this->assertNull($prediction->saturatedFat->perServing);
        $this->assertNull($prediction->transFat->dailyValue);
        $this->assertNull($prediction->transFat->per100G);
        $this->assertNull($prediction->transFat->perServing);
        $this->assertNull($prediction->cholesterol->dailyValue);
        $this->assertNull($prediction->cholesterol->per100G);
        $this->assertNull($prediction->cholesterol->perServing);
        $this->assertNull($prediction->totalCarbohydrate->dailyValue);
        $this->assertNull($prediction->totalCarbohydrate->per100G);
        $this->assertNull($prediction->totalCarbohydrate->perServing);
        $this->assertNull($prediction->dietaryFiber->dailyValue);
        $this->assertNull($prediction->dietaryFiber->per100G);
        $this->assertNull($prediction->dietaryFiber->perServing);
        $this->assertNull($prediction->totalSugars->dailyValue);
        $this->assertNull($prediction->totalSugars->per100G);
        $this->assertNull($prediction->totalSugars->perServing);
        $this->assertNull($prediction->addedSugars->dailyValue);
        $this->assertNull($prediction->addedSugars->per100G);
        $this->assertNull($prediction->addedSugars->perServing);
        $this->assertNull($prediction->protein->dailyValue);
        $this->assertNull($prediction->protein->per100G);
        $this->assertNull($prediction->protein->perServing);
        $this->assertNull($prediction->sodium->dailyValue);
        $this->assertNull($prediction->sodium->per100G);
        $this->assertNull($prediction->sodium->perServing);
        $this->assertNull($prediction->sodium->unit);
        $this->assertEquals(0, count($prediction->nutrients));
    }
}
