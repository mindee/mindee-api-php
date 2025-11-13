<?php

namespace V1\Product\Resume;

use Mindee\Parsing\Common\Document;
use Mindee\Product\Resume;
use PHPUnit\Framework\TestCase;

class ResumeV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/resume/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(Resume\ResumeV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(Resume\ResumeV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->documentLanguage->value);
        $this->assertEquals(0, count($prediction->givenNames));
        $this->assertEquals(0, count($prediction->surnames));
        $this->assertNull($prediction->nationality->value);
        $this->assertNull($prediction->emailAddress->value);
        $this->assertNull($prediction->phoneNumber->value);
        $this->assertNull($prediction->address->value);
        $this->assertEquals(0, count($prediction->socialNetworksUrls));
        $this->assertNull($prediction->profession->value);
        $this->assertNull($prediction->jobApplied->value);
        $this->assertEquals(0, count($prediction->languages));
        $this->assertEquals(0, count($prediction->hardSkills));
        $this->assertEquals(0, count($prediction->softSkills));
        $this->assertEquals(0, count($prediction->education));
        $this->assertEquals(0, count($prediction->professionalExperiences));
        $this->assertEquals(0, count($prediction->certificates));
    }
}
