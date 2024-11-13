---
title: Delivery note OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-delivery-note-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Delivery note API](https://platform.mindee.com/mindee/delivery_notes).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/delivery_notes/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Delivery note sample](https://github.com/mindee/client-lib-test-data/blob/main/products/delivery_notes/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\DeliveryNote\DeliveryNoteV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(DeliveryNoteV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: d5ead821-edec-4d31-a69a-cf3998d9a506
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/delivery_notes v1.0
:Rotation applied: Yes

Prediction
==========
:Delivery Date: 2019-10-02
:Delivery Number: INT-001
:Supplier Name: John Smith
:Supplier Address: 4490 Oak Drive, Albany, NY 12210
:Customer Name: Jessie M Horne
:Customer Address: 4312 Wood Road, New York, NY 10031
:Total Amount: 204.75
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


### AmountField
The amount field `AmountField` only has one constraint: its **value** is an optional `?float`.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

# Attributes
The following fields are extracted for Delivery note V1:

## Customer Address
**customerAddress** : The address of the customer receiving the goods.

```php
echo $result->document->inference->prediction->customerAddress->value;
```

## Customer Name
**customerName** : The name of the customer receiving the goods.

```php
echo $result->document->inference->prediction->customerName->value;
```

## Delivery Date
**deliveryDate** : The date on which the delivery is scheduled to arrive.

```php
echo $result->document->inference->prediction->deliveryDate->value;
```

## Delivery Number
**deliveryNumber** : A unique identifier for the delivery note.

```php
echo $result->document->inference->prediction->deliveryNumber->value;
```

## Supplier Address
**supplierAddress** : The address of the supplier providing the goods.

```php
echo $result->document->inference->prediction->supplierAddress->value;
```

## Supplier Name
**supplierName** : The name of the supplier providing the goods.

```php
echo $result->document->inference->prediction->supplierName->value;
```

## Total Amount
**totalAmount** : The total monetary value of the goods being delivered.

```php
echo $result->document->inference->prediction->totalAmount->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
