---
title: Cropper OCR PHP
---
The PHP OCR SDK supports the [Cropper API](https://platform.mindee.com/mindee/cropper).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/cropper/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Cropper sample](https://github.com/mindee/client-lib-test-data/blob/main/products/cropper/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Cropper\CropperV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(CropperV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 149ce775-8302-4798-8649-7eda9fb84a1a
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/cropper v1.0
:Rotation applied: No

Prediction
==========

Page Predictions
================

Page 0
------
:Document Cropper: Polygon with 26 points.
                   Polygon with 25 points.
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

## Page-Level Fields
Some fields are constrained to the page level, and so will not be retrievable to through the document.

# Attributes
The following fields are extracted for Cropper V1:

## Document Cropper
[📄](#page-level-fields "This field is only present on individual pages.")**cropping** : List of documents found in the image.

```php
foreach ($result->document->inference->pages as $page)
{
    foreach ($page->prediction->cropping as $croppingElem)
    {
        echo $croppingElem->polygon;
        echo $croppingElem->quadrangle;
        echo $croppingElem->rectangle;
        echo $croppingElem->boundingBox;
    }
}->polygon;
    echo $croppingElem->quadrangle;
    echo $croppingElem->rectangle;
    echo $croppingElem->boundingBox;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
