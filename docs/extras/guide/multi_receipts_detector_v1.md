---
title: Multi Receipts Detector OCR PHP
---
The PHP OCR SDK supports the [Multi Receipts Detector API](https://platform.mindee.com/mindee/multi_receipts_detector).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/multi_receipts_detector/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Multi Receipts Detector sample](https://github.com/mindee/client-lib-test-data/blob/main/products/multi_receipts_detector/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\MultiReceiptsDetector\MultiReceiptsDetectorV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(MultiReceiptsDetectorV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: d7c5b25f-e0d3-4491-af54-6183afa1aaab
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/multi_receipts_detector v1.0
:Rotation applied: Yes

Prediction
==========
:List of Receipts: Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.

Page Predictions
================

Page 0
------
:List of Receipts: Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
                   Polygon with 4 points.
```

# Field Types
## Standard Fields
These fields are generic and used in several products.

### BasicField
Each prediction object contains a set of fields that inherit from the generic `BaseField` class.
A typical `BaseField` object will have the following attributes:

* **value** (`float|string`): corresponds to the field value. Can be `null` if no value was extracted.
* **confidence** (`float`): the confidence score of the field prediction.
* **boundingBox** (`[Point, Point, Point, Point]`): contains exactly 4 relative vertices (points) coordinates of a right rectangle containing the field in the document.
* **polygon** (`Point[]`): contains the relative vertices coordinates (`Point`) of a polygon containing the field in the image.
* **pageId** (`integer`): the ID of the page, is `null` when at document-level.
* **reconstructed** (`bool`): indicates whether an object was reconstructed (not extracted as the API gave it).

> **Note:** A `Point` simply refers to a List of two numbers (`[float, float]`).


Aside from the previous attributes, all basic fields have access to a custom `__toString` method that can be used to print their value as a string.


### PositionField
The position field `PositionField` does not implement all the basic `BaseField` attributes, only **boundingBox**, **polygon** and **pageId**. On top of these, it has access to:

* **rectangle** (`[Point, Point, Point, Point]`): a Polygon with four points that may be oriented (even beyond canvas).
* **quadrangle** (`[Point, Point, Point, Point]`): a free polygon made up of four points.

# Attributes
The following fields are extracted for Multi Receipts Detector V1:

## List of Receipts
**receipts** : Positions of the receipts on the document.

```php
foreach ($result->document->inference->prediction->receipts as $receiptsElem)
{
    echo $receiptsElem->polygon;
    echo $receiptsElem->quadrangle;
    echo $receiptsElem->rectangle;
    echo $receiptsElem->boundingBox;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
