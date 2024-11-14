<?php

namespace Product\Ind\IndianPassport;

use Mindee\Product\Ind\IndianPassport;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class IndianPassportV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/ind_passport/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(IndianPassport\IndianPassportV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(IndianPassport\IndianPassportV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->country->value);
        $this->assertNull($prediction->idNumber->value);
        $this->assertNull($prediction->givenNames->value);
        $this->assertNull($prediction->surname->value);
        $this->assertNull($prediction->birthDate->value);
        $this->assertNull($prediction->birthPlace->value);
        $this->assertNull($prediction->issuancePlace->value);
        $this->assertNull($prediction->issuanceDate->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->mrz1->value);
        $this->assertNull($prediction->mrz2->value);
        $this->assertNull($prediction->legalGuardian->value);
        $this->assertNull($prediction->nameOfSpouse->value);
        $this->assertNull($prediction->nameOfMother->value);
        $this->assertNull($prediction->oldPassportDateOfIssue->value);
        $this->assertNull($prediction->oldPassportNumber->value);
        $this->assertNull($prediction->address1->value);
        $this->assertNull($prediction->address2->value);
        $this->assertNull($prediction->address3->value);
        $this->assertNull($prediction->oldPassportPlaceOfIssue->value);
        $this->assertNull($prediction->fileNumber->value);
    }
}
