<?php

namespace Product\Custom;

use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use Mindee\Parsing\Common\Prediction;
use Mindee\Product\Custom\CustomV1;
use Mindee\Product\Custom\CustomV1Document;
use Mindee\Product\Custom\CustomV1Page;
use PHPUnit\Framework\TestCase;
use Product\ProductSharedData;

class CustomV1LineItemsTest extends TestCase
{
    private array $anchors;
    private array $columns;
    private Prediction $customV1LineItemsDocV1;
    private Prediction $customV1LineItemsPage0V1;
    private Prediction $customV1LineItemsDocV2;
    private Prediction $customV1LineItemsPage0V2;

    protected function setUp(): void
    {
        $jsonV1Complete = file_get_contents(
            ProductSharedData::getProductDataDir() . "custom/response_v1/line_items/single_table_01.json"
        );
        $rawDataV1Complete = json_decode($jsonV1Complete, true);
        $lineItemsDoc = new Document(CustomV1::class, $rawDataV1Complete["document"]);
        $this->customV1LineItemsDocV1 = $lineItemsDoc->inference->prediction;
        $page = new Page(
            CustomV1Page::class,
            $rawDataV1Complete["document"]["inference"]["pages"][0]
        );
        $this->customV1LineItemsPage0V1 = $page->prediction;

        $jsonV1CompleteV2 = file_get_contents(
            ProductSharedData::getProductDataDir() . "custom/response_v2/line_items/single_table_01.json"
        );
        $rawDataV1CompleteV2 = json_decode($jsonV1CompleteV2, true);
        $lineItemsDocV2 = new Document(CustomV1::class, $rawDataV1CompleteV2["document"]);
        $this->customV1LineItemsDocV2 = $lineItemsDocV2->inference->prediction;
        $pageV2 = new Page(
            CustomV1Page::class,
            $rawDataV1CompleteV2["document"]["inference"]["pages"][0]
        );
        $this->customV1LineItemsPage0V2 = $pageV2->prediction;
        $this->anchors = ["beneficiary_name"];
        $this->columns = [
            "beneficiary_birth_date",
            "beneficiary_number",
            "beneficiary_name",
            "beneficiary_rank",
        ];
    }

    public function testSingleTable01()
    {
        assert($this->customV1LineItemsDocV1 instanceof CustomV1Document);
        $lineItemsDoc = $this->customV1LineItemsDocV1->columnsToLineItems($this->anchors, $this->columns, 0.011);
        assert($this->customV1LineItemsPage0V1 instanceof CustomV1Page);
        $lineItemsPage = $this->customV1LineItemsPage0V1->columnsToLineItems($this->anchors, $this->columns, 0.011);
        foreach ([$lineItemsDoc, $lineItemsPage] as $lineItems) {
            $this->assertEquals(3, count($lineItems));

            $this->assertEquals("JAMES BOND 007", $lineItems[0]->fields["beneficiary_name"]->content);
            $this->assertEquals("1970-11-11", $lineItems[0]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(1, $lineItems[0]->rowNumber);

            $this->assertEquals("HARRY POTTER", $lineItems[1]->fields["beneficiary_name"]->content);
            $this->assertEquals("2010-07-18", $lineItems[1]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(2, $lineItems[1]->rowNumber);

            $this->assertEquals("DRAGO MALFOY", $lineItems[2]->fields["beneficiary_name"]->content);
            $this->assertEquals("2015-07-05", $lineItems[2]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(3, $lineItems[2]->rowNumber);
        }
    }

    public function testSingleTable02()
    {
        assert($this->customV1LineItemsDocV2 instanceof CustomV1Document);
        $lineItemsDoc = $this->customV1LineItemsDocV2->columnsToLineItems($this->anchors, $this->columns, 0.011);
        assert($this->customV1LineItemsPage0V2 instanceof CustomV1Page);
        $lineItemsPage = $this->customV1LineItemsPage0V2->columnsToLineItems($this->anchors, $this->columns, 0.011);
        foreach ([$lineItemsDoc, $lineItemsPage] as $lineItems) {
            $this->assertEquals(3, count($lineItems));

            $this->assertEquals("JAMES BOND 007", $lineItems[0]->fields["beneficiary_name"]->content);
            $this->assertEquals("1970-11-11", $lineItems[0]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(1, $lineItems[0]->rowNumber);

            $this->assertEquals("HARRY POTTER", $lineItems[1]->fields["beneficiary_name"]->content);
            $this->assertEquals("2010-07-18", $lineItems[1]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(2, $lineItems[1]->rowNumber);

            $this->assertEquals("DRAGO MALFOY", $lineItems[2]->fields["beneficiary_name"]->content);
            $this->assertEquals("2015-07-05", $lineItems[2]->fields["beneficiary_birth_date"]->content);
            $this->assertEquals(3, $lineItems[2]->rowNumber);
        }
    }
}
