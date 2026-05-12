<?php

declare(strict_types=1);

namespace V1\Parsing\Common\Extras;

use Mindee\V1\ClientOptions\PredictOptions;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Receipt\ReceiptV5;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class CropperExtraTest extends TestCase
{
    private string $cropperDir;
    private Document $completeDoc;
    protected function setUp(): void
    {
        $this->cropperDir = TestingUtilities::getV1DataDir() . "/extras/cropper/";
        $completeDocFile = file_get_contents($this->cropperDir . "complete.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $this->completeDoc = new Document(ReceiptV5::class, $completeDocJSON["document"]);
    }
    public function testEnqueuingCropperEnqueuesCropper(): void
    {
        $predictOptions = new PredictOptions();
        $predictOptions->setCropper(true);
        self::assertTrue($predictOptions->cropper);
    }

    public function testCropperExtra(): void
    {
        self::assertCount(1, $this->completeDoc->inference->pages[0]->extras->cropper->croppings);
        self::assertSame(0.057, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[0]->getX());
        self::assertSame(0.008, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[0]->getY());
        self::assertSame(0.846, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[1]->getX());
        self::assertSame(0.008, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[1]->getY());
        self::assertSame(0.846, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[2]->getX());
        self::assertSame(1.0, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[2]->getY());
        self::assertSame(0.057, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[3]->getX());
        self::assertSame(1.0, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[3]->getY());
        self::assertCount(24, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->polygon->getCoordinates());
        self::assertCount(4, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->quadrangle->getCoordinates());
        self::assertCount(4, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->rectangle->getCoordinates());
    }
}
