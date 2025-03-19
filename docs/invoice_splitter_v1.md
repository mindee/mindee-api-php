---
title: Invoice Splitter OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-invoice-splitter-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Invoice Splitter API](https://platform.mindee.com/mindee/invoice_splitter).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/invoice_splitter/default_sample.pdf), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Invoice Splitter sample](https://github.com/mindee/client-lib-test-data/blob/main/products/invoice_splitter/default_sample.pdf?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(InvoiceSplitterV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 15ad7a19-7b75-43d0-b0c6-9a641a12b49b
:Filename: default_sample.pdf

Inference
#########
:Product: mindee/invoice_splitter v1.1
:Rotation applied: No

Prediction
==========
:Invoice Page Groups:
  :Page indexes: 0
  :Page indexes: 1

Page Predictions
================

Page 0
------
:Invoice Page Groups:

Page 1
------
:Invoice Page Groups:
```

# Field Types
## Standard Fields
These fields are generic and used in several products.

### BaseField
Each prediction object contains a set of fields that inherit from the generic `BaseField` class.
A typical `BaseField` object will have the following attributes:

* **value** (`float|string`): corresponds to the field value. Can be `null` if no value was extracted.
* **confidence** (`float`): the confidence score of the field prediction.
* **boundingBox** (`[Point, Point, Point, Point]`): contains exactly 4 relative vertices (points) coordinates of a right rectangle containing the field in the document.
* **polygon** (`Point[]`): contains the relative vertices coordinates (`Point`) of a polygon containing the field in the image.
* **pageId** (`integer`): the ID of the page, always `null` when at document-level.
* **reconstructed** (`bool`): indicates whether an object was reconstructed (not extracted as the API gave it).

> **Note:** A `Point` simply refers to a list of two numbers (`[float, float]`).


Aside from the previous attributes, all basic fields have access to a custom `__toString` method that can be used to print their value as a string.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Invoice Page Groups Field
List of page groups. Each group represents a single invoice within a multi-invoice document.

A `InvoiceSplitterV1InvoicePageGroup` implements the following attributes:

* **pageIndexes** (`int`): List of page indexes that belong to the same invoice (group).

# Attributes
The following fields are extracted for Invoice Splitter V1:

## Invoice Page Groups
**invoicePageGroups** ([[InvoiceSplitterV1InvoicePageGroup](#invoice-page-groups-field)]): List of page groups. Each group represents a single invoice within a multi-invoice document.

```php
foreach ($result->document->inference->prediction->invoicePageGroups as $invoicePageGroupsElem)
{
    echo $invoicePageGroupsElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
