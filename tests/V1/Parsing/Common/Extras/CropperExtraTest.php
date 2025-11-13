<?php

namespace V1\Parsing\Common\Extras;

use Mindee\Input\PredictOptions;
use Mindee\Parsing\Common\Document;
use Mindee\Product\Receipt\ReceiptV5;
use PHPUnit\Framework\TestCase;

class CropperExtraTest extends TestCase
{
    private string $cropperDir;
    private Document $completeDoc;
    protected function setUp(): void
    {
        $this->cropperDir = \TestingUtilities::getV1DataDir() . "/extras/cropper/";
        $completeDocFile = file_get_contents($this->cropperDir . "complete.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $this->completeDoc = new Document(ReceiptV5::class, $completeDocJSON["document"]);
    }
    public function testEnqueuingCropperEnqueuesCropper()
    {
        $predictOptions = new PredictOptions();
        $predictOptions->setCropper(true);
        $this->assertTrue($predictOptions->cropper);
    }

    public function testCropperExtra()
    {
        $this->assertEquals(1, count($this->completeDoc->inference->pages[0]->extras->cropper->croppings));
        $this->assertEquals(0.057, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[0]->getX());
        $this->assertEquals(0.008, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[0]->getY());
        $this->assertEquals(0.846, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[1]->getX());
        $this->assertEquals(0.008, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[1]->getY());
        $this->assertEquals(0.846, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[2]->getX());
        $this->assertEquals(1.0, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[2]->getY());
        $this->assertEquals(0.057, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[3]->getX());
        $this->assertEquals(1.0, $this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->boundingBox->getCoordinates()[3]->getY());
        $this->assertEquals(24, count($this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->polygon->getCoordinates()));
        $this->assertEquals(4, count($this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->quadrangle->getCoordinates()));
        $this->assertEquals(4, count($this->completeDoc->inference->pages[0]->extras->cropper->croppings[0]->rectangle->getCoordinates()));
    }
}
