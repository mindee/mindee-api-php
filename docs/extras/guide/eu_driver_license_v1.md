---
title: EU Driver License OCR PHP
---
The PHP OCR SDK supports the [Driver License API](https://platform.mindee.com/mindee/eu_driver_license).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/eu_driver_license/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Driver License sample](https://github.com/mindee/client-lib-test-data/blob/main/products/eu_driver_license/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Eu\DriverLicense\DriverLicenseV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(DriverLicenseV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: b19cc32e-b3e6-4ff9-bdc7-619199355d54
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/eu_driver_license v1.0
:Rotation applied: Yes

Prediction
==========
:Country Code: FR
:Document ID: 13AA00002
:Driver License Category: AM A1 B1 B D BE DE
:Last Name: MARTIN
:First Name: PAUL
:Date Of Birth: 1981-07-14
:Place Of Birth: Utopiacity
:Expiry Date: 2018-12-31
:Issue Date: 2013-01-01
:Issue Authority: 99999UpiaCity
:MRZ: D1FRA13AA000026181231MARTIN<<9
:Address:

Page Predictions
================

Page 0
------
:Photo: Polygon with 4 points.
:Signature: Polygon with 4 points.
:Country Code: FR
:Document ID: 13AA00002
:Driver License Category: AM A1 B1 B D BE DE
:Last Name: MARTIN
:First Name: PAUL
:Date Of Birth: 1981-07-14
:Place Of Birth: Utopiacity
:Expiry Date: 2018-12-31
:Issue Date: 2013-01-01
:Issue Authority: 99999UpiaCity
:MRZ: D1FRA13AA000026181231MARTIN<<9
:Address:
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

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.


### PositionField
The position field `PositionField` does not implement all the basic `BaseField` attributes, only **boundingBox**, **polygon** and **pageId**. On top of these, it has access to:

* **rectangle** (`[Point, Point, Point, Point]`): a Polygon with four points that may be oriented (even beyond canvas).
* **quadrangle** (`[Point, Point, Point, Point]`): a free polygon made up of four points.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

## Page-Level Fields
Some fields are constrained to the page level, and so will not be retrievable to through the document.

# Attributes
The following fields are extracted for Driver License V1:

## Address
**address** : EU driver license holders address

```php
echo $result->document->inference->prediction->address->value;
```

## Driver License Category
**category** : EU driver license holders categories

```php
echo $result->document->inference->prediction->category->value;
```

## Country Code
**countryCode** : Country code extracted as a string.

```php
echo $result->document->inference->prediction->countryCode->value;
```

## Date Of Birth
**dateOfBirth** : The date of birth of the document holder

```php
echo $result->document->inference->prediction->dateOfBirth->value;
```

## Document ID
**documentId** : ID number of the Document.

```php
echo $result->document->inference->prediction->documentId->value;
```

## Expiry Date
**expiryDate** : Date the document expires

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## First Name
**firstName** : First name(s) of the driver license holder

```php
echo $result->document->inference->prediction->firstName->value;
```

## Issue Authority
**issueAuthority** : Authority that issued the document

```php
echo $result->document->inference->prediction->issueAuthority->value;
```

## Issue Date
**issueDate** : Date the document was issued

```php
echo $result->document->inference->prediction->issueDate->value;
```

## Last Name
**lastName** : Last name of the driver license holder.

```php
echo $result->document->inference->prediction->lastName->value;
```

## MRZ
**mrz** : Machine-readable license number

```php
echo $result->document->inference->prediction->mrz->value;
```

## Photo
[📄](#page-level-fields "This field is only present on individual pages.")**photo** : Has a photo of the EU driver license holder

```php
foreach($result->document->photo as $photoElem){
    echo $photoElem;
}->polygon->getCoordinates();
```

## Place Of Birth
**placeOfBirth** : Place where the driver license holder was born

```php
echo $result->document->inference->prediction->placeOfBirth->value;
```

## Signature
[📄](#page-level-fields "This field is only present on individual pages.")**signature** : Has a signature of the EU driver license holder

```php
foreach($result->document->signature as $signatureElem){
    echo $signatureElem;
}->polygon->getCoordinates();
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
