<?php

declare(strict_types=1);

namespace V1\Product\NutritionFactsLabel;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\NutritionFactsLabel\NutritionFactsLabelV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class NutritionFactsLabelV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/nutrition_facts/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(NutritionFactsLabelV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(NutritionFactsLabelV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->servingPerBox->value);
        self::assertNull($prediction->servingSize->amount);
        self::assertNull($prediction->servingSize->unit);
        self::assertNull($prediction->calories->dailyValue);
        self::assertNull($prediction->calories->per100G);
        self::assertNull($prediction->calories->perServing);
        self::assertNull($prediction->totalFat->dailyValue);
        self::assertNull($prediction->totalFat->per100G);
        self::assertNull($prediction->totalFat->perServing);
        self::assertNull($prediction->saturatedFat->dailyValue);
        self::assertNull($prediction->saturatedFat->per100G);
        self::assertNull($prediction->saturatedFat->perServing);
        self::assertNull($prediction->transFat->dailyValue);
        self::assertNull($prediction->transFat->per100G);
        self::assertNull($prediction->transFat->perServing);
        self::assertNull($prediction->cholesterol->dailyValue);
        self::assertNull($prediction->cholesterol->per100G);
        self::assertNull($prediction->cholesterol->perServing);
        self::assertNull($prediction->totalCarbohydrate->dailyValue);
        self::assertNull($prediction->totalCarbohydrate->per100G);
        self::assertNull($prediction->totalCarbohydrate->perServing);
        self::assertNull($prediction->dietaryFiber->dailyValue);
        self::assertNull($prediction->dietaryFiber->per100G);
        self::assertNull($prediction->dietaryFiber->perServing);
        self::assertNull($prediction->totalSugars->dailyValue);
        self::assertNull($prediction->totalSugars->per100G);
        self::assertNull($prediction->totalSugars->perServing);
        self::assertNull($prediction->addedSugars->dailyValue);
        self::assertNull($prediction->addedSugars->per100G);
        self::assertNull($prediction->addedSugars->perServing);
        self::assertNull($prediction->protein->dailyValue);
        self::assertNull($prediction->protein->per100G);
        self::assertNull($prediction->protein->perServing);
        self::assertNull($prediction->sodium->dailyValue);
        self::assertNull($prediction->sodium->per100G);
        self::assertNull($prediction->sodium->perServing);
        self::assertNull($prediction->sodium->unit);
        self::assertCount(0, $prediction->nutrients);
    }
}
