<?php

declare(strict_types=1);

namespace V1\Product\Resume;

use Mindee\Product\Resume;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Resume\ResumeV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class ResumeV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/resume/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(ResumeV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(ResumeV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->documentLanguage->value);
        self::assertCount(0, $prediction->givenNames);
        self::assertCount(0, $prediction->surnames);
        self::assertNull($prediction->nationality->value);
        self::assertNull($prediction->emailAddress->value);
        self::assertNull($prediction->phoneNumber->value);
        self::assertNull($prediction->address->value);
        self::assertCount(0, $prediction->socialNetworksUrls);
        self::assertNull($prediction->profession->value);
        self::assertNull($prediction->jobApplied->value);
        self::assertCount(0, $prediction->languages);
        self::assertCount(0, $prediction->hardSkills);
        self::assertCount(0, $prediction->softSkills);
        self::assertCount(0, $prediction->education);
        self::assertCount(0, $prediction->professionalExperiences);
        self::assertCount(0, $prediction->certificates);
    }
}
