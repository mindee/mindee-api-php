---
title: Invoice Splitter API PHP
---
The PHP OCR SDK supports the [Invoice Splitter API](https://platform.mindee.com/mindee/invoice_splitter).

Using [this sample](https://github.com/mindee/client-lib-test-data/blob/main/products/invoice_splitter/default_sample.pdf), we are going to illustrate how to detect the pages of multiple invoices within the same document.

# Quick-Start

> **⚠️ Important:** This API only works **asynchronously**, which means that documents have to be sent and retrieved in a specific way:

```php
<?php

use Mindee\Client;
use Mindee\Product\InvoiceSplitter\InvoiceSplitterV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Enqueue and parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(InvoiceSplitterV1::class, $inputSource);

echo strval($apiResponse->document);

```

**Output (RST):**

```rst
########
Document
########
:Mindee ID: 8c25cc63-212b-4537-9c9b-3fbd3bd0ee20
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/carte_vitale v1.0
:Rotation applied: Yes

Prediction
==========
:Given Name(s): NATHALIE
:Surname: DURAND
:Social Security Number: 269054958815780
:Issuance Date: 2007-01-01

Page Predictions
================

Page 0
------
:Given Name(s): NATHALIE
:Surname: DURAND
:Social Security Number: 269054958815780
:Issuance Date: 2007-01-01
```

# Field Types

## Specific Fields

### Page Group

List of page group indexes.

An `InvoiceSplitterV1PageGroup` implements the following attributes:

- **pageIndexes** (`float`\[]): List of indexes of the pages of a single invoice.
- **confidence** (`float`): The confidence of the prediction.

# Attributes

The following fields are extracted for Invoice Splitter V1:

## Invoice Page Groups

**InvoiceSplitterV1PageGroup** ([InvoiceSplitterV1PageGroup](#invoice-splitter-v1-page-group)[]): List of page indexes that belong to the same invoice in the PDF.

```php
foreach ($page->prediction->invoicePageGroups as $invoicePageGroupsElem)
{
    echo $invoicePageGroupsElem;
}
```

# Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
