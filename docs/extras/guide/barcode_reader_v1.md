---
title: Barcode Reader OCR PHP
---
The PHP OCR SDK supports the [Barcode Reader API](https://platform.mindee.com/mindee/barcode_reader).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/barcode_reader/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Barcode Reader sample](https://github.com/mindee/client-lib-test-data/blob/main/products/barcode_reader/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\BarcodeReader\BarcodeReaderV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(BarcodeReaderV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: f9c48da1-a306-4805-8da8-f7231fda2d88
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/barcode_reader v1.0
:Rotation applied: Yes

Prediction
==========
:Barcodes 1D: Mindee
:Barcodes 2D: https://developers.mindee.com/docs/barcode-reader-ocr
              I love paperwork! - Said no one ever

Page Predictions
================

Page 0
------
:Barcodes 1D: Mindee
:Barcodes 2D: https://developers.mindee.com/docs/barcode-reader-ocr
              I love paperwork! - Said no one ever
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

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

# Attributes
The following fields are extracted for Barcode Reader V1:

## Barcodes 1D
**codes1D** : List of decoded 1D barcodes.

```php
foreach ($result->document->inference->prediction->codes1D as $codes1DElem)
{
    echo $codes1DElem->value;
}
```

## Barcodes 2D
**codes2D** : List of decoded 2D barcodes.

```php
foreach ($result->document->inference->prediction->codes2D as $codes2DElem)
{
    echo $codes2DElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
