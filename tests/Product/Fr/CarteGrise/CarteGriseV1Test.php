<?php

namespace Product\Fr\CarteGrise;

use Mindee\Product\Fr\CarteGrise;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class CarteGriseV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/carte_grise/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(CarteGrise\CarteGriseV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(CarteGrise\CarteGriseV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->a->value);
        $this->assertNull($prediction->b->value);
        $this->assertNull($prediction->c1->value);
        $this->assertNull($prediction->c3->value);
        $this->assertNull($prediction->c41->value);
        $this->assertNull($prediction->c4A->value);
        $this->assertNull($prediction->d1->value);
        $this->assertNull($prediction->d3->value);
        $this->assertNull($prediction->e->value);
        $this->assertNull($prediction->f1->value);
        $this->assertNull($prediction->f2->value);
        $this->assertNull($prediction->f3->value);
        $this->assertNull($prediction->g->value);
        $this->assertNull($prediction->g1->value);
        $this->assertNull($prediction->i->value);
        $this->assertNull($prediction->j->value);
        $this->assertNull($prediction->j1->value);
        $this->assertNull($prediction->j2->value);
        $this->assertNull($prediction->j3->value);
        $this->assertNull($prediction->p1->value);
        $this->assertNull($prediction->p2->value);
        $this->assertNull($prediction->p3->value);
        $this->assertNull($prediction->p6->value);
        $this->assertNull($prediction->q->value);
        $this->assertNull($prediction->s1->value);
        $this->assertNull($prediction->s2->value);
        $this->assertNull($prediction->u1->value);
        $this->assertNull($prediction->u2->value);
        $this->assertNull($prediction->v7->value);
        $this->assertNull($prediction->x1->value);
        $this->assertNull($prediction->y1->value);
        $this->assertNull($prediction->y2->value);
        $this->assertNull($prediction->y3->value);
        $this->assertNull($prediction->y4->value);
        $this->assertNull($prediction->y5->value);
        $this->assertNull($prediction->y6->value);
        $this->assertNull($prediction->formulaNumber->value);
        $this->assertNull($prediction->ownerFirstName->value);
        $this->assertNull($prediction->ownerSurname->value);
        $this->assertNull($prediction->mrz1->value);
        $this->assertNull($prediction->mrz2->value);
    }
}
