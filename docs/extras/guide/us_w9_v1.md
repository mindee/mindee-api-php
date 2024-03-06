---
title: US W9 OCR PHP
---
The PHP OCR SDK supports the [W9 API](https://platform.mindee.com/mindee/us_w9).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/us_w9/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![W9 sample](https://github.com/mindee/client-lib-test-data/blob/main/products/us_w9/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Us\W9\W9V1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(W9V1::class, $inputSource);

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
:Product: mindee/us_w9 v1.0
:Rotation applied: Yes

Prediction
==========

Page Predictions
================

Page 0
------
:Name: Stephen W Hawking
:SSN: 560758145
:Address: Somewhere In Milky Way
:City State Zip: Probably Still At Cambridge P O Box CB1
:Business Name:
:EIN: 942203664
:Tax Classification: individual
:Tax Classification Other Details:
:W9 Revision Date: august 2013
:Signature Position: Polygon with 4 points.
:Signature Date Position:
:Tax Classification LLC:
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

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

## Page-Level Fields
Some fields are constrained to the page level, and so will not be retrievable to through the document.

# Attributes
The following fields are extracted for W9 V1:

## Address
[📄](#page-level-fields "This field is only present on individual pages.")**address** : The street address (number, street, and apt. or suite no.) of the applicant.

```php
foreach($result->document->address as $addressElem){
    echo $addressElem;
}->value;
```

## Business Name
[📄](#page-level-fields "This field is only present on individual pages.")**businessName** : The business name or disregarded entity name, if different from Name.

```php
foreach($result->document->businessName as $businessNameElem){
    echo $businessNameElem;
}->value;
```

## City State Zip
[📄](#page-level-fields "This field is only present on individual pages.")**cityStateZip** : The city, state, and ZIP code of the applicant.

```php
foreach($result->document->cityStateZip as $cityStateZipElem){
    echo $cityStateZipElem;
}->value;
```

## EIN
[📄](#page-level-fields "This field is only present on individual pages.")**ein** : The employer identification number.

```php
foreach($result->document->ein as $einElem){
    echo $einElem;
}->value;
```

## Name
[📄](#page-level-fields "This field is only present on individual pages.")**name** : Name as shown on the applicant's income tax return.

```php
foreach($result->document->name as $nameElem){
    echo $nameElem;
}->value;
```

## Signature Date Position
[📄](#page-level-fields "This field is only present on individual pages.")**signatureDatePosition** : Position of the signature date on the document.

```php
foreach($result->document->signatureDatePosition as $signatureDatePositionElem){
    echo $signatureDatePositionElem;
}->polygon->getCoordinates();
```

## Signature Position
[📄](#page-level-fields "This field is only present on individual pages.")**signaturePosition** : Position of the signature on the document.

```php
foreach($result->document->signaturePosition as $signaturePositionElem){
    echo $signaturePositionElem;
}->polygon->getCoordinates();
```

## SSN
[📄](#page-level-fields "This field is only present on individual pages.")**ssn** : The applicant's social security number.

```php
foreach($result->document->ssn as $ssnElem){
    echo $ssnElem;
}->value;
```

## Tax Classification
[📄](#page-level-fields "This field is only present on individual pages.")**taxClassification** : The federal tax classification, which can vary depending on the revision date.

```php
foreach($result->document->taxClassification as $taxClassificationElem){
    echo $taxClassificationElem;
}->value;
```

## Tax Classification LLC
[📄](#page-level-fields "This field is only present on individual pages.")**taxClassificationLlc** : Depending on revision year, among S, C, P or D for Limited Liability Company Classification.

```php
foreach($result->document->taxClassificationLlc as $taxClassificationLlcElem){
    echo $taxClassificationLlcElem;
}->value;
```

## Tax Classification Other Details
[📄](#page-level-fields "This field is only present on individual pages.")**taxClassificationOtherDetails** : Tax Classification Other Details.

```php
foreach($result->document->taxClassificationOtherDetails as $taxClassificationOtherDetailsElem){
    echo $taxClassificationOtherDetailsElem;
}->value;
```

## W9 Revision Date
[📄](#page-level-fields "This field is only present on individual pages.")**w9RevisionDate** : The Revision month and year of the W9 form.

```php
foreach($result->document->w9RevisionDate as $w9RevisionDateElem){
    echo $w9RevisionDateElem;
}->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
