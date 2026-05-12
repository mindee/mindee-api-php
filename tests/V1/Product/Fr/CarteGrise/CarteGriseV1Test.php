<?php

declare(strict_types=1);

namespace V1\Product\Fr\CarteGrise;

use Mindee\Product\Fr\CarteGrise;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Fr\CarteGrise\CarteGriseV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class CarteGriseV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/carte_grise/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(CarteGriseV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(CarteGriseV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->a->value);
        self::assertNull($prediction->b->value);
        self::assertNull($prediction->c1->value);
        self::assertNull($prediction->c3->value);
        self::assertNull($prediction->c41->value);
        self::assertNull($prediction->c4A->value);
        self::assertNull($prediction->d1->value);
        self::assertNull($prediction->d3->value);
        self::assertNull($prediction->e->value);
        self::assertNull($prediction->f1->value);
        self::assertNull($prediction->f2->value);
        self::assertNull($prediction->f3->value);
        self::assertNull($prediction->g->value);
        self::assertNull($prediction->g1->value);
        self::assertNull($prediction->i->value);
        self::assertNull($prediction->j->value);
        self::assertNull($prediction->j1->value);
        self::assertNull($prediction->j2->value);
        self::assertNull($prediction->j3->value);
        self::assertNull($prediction->p1->value);
        self::assertNull($prediction->p2->value);
        self::assertNull($prediction->p3->value);
        self::assertNull($prediction->p6->value);
        self::assertNull($prediction->q->value);
        self::assertNull($prediction->s1->value);
        self::assertNull($prediction->s2->value);
        self::assertNull($prediction->u1->value);
        self::assertNull($prediction->u2->value);
        self::assertNull($prediction->v7->value);
        self::assertNull($prediction->x1->value);
        self::assertNull($prediction->y1->value);
        self::assertNull($prediction->y2->value);
        self::assertNull($prediction->y3->value);
        self::assertNull($prediction->y4->value);
        self::assertNull($prediction->y5->value);
        self::assertNull($prediction->y6->value);
        self::assertNull($prediction->formulaNumber->value);
        self::assertNull($prediction->ownerFirstName->value);
        self::assertNull($prediction->ownerSurname->value);
        self::assertNull($prediction->mrz1->value);
        self::assertNull($prediction->mrz2->value);
    }
}
