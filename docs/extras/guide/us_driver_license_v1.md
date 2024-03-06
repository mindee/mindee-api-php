---
title: US Driver License OCR PHP
---
The PHP OCR SDK supports the [Driver License API](https://platform.mindee.com/mindee/us_driver_license).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/us_driver_license/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Driver License sample](https://github.com/mindee/client-lib-test-data/blob/main/products/us_driver_license/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Us\DriverLicense\DriverLicenseV1;

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
:Mindee ID: bf70068d-d3d6-49dc-b93a-b4b7d156fc3d
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/us_driver_license v1.0
:Rotation applied: Yes

Prediction
==========
:State: AZ
:Driver License ID: D12345678
:Expiry Date: 2018-02-01
:Date Of Issue: 2013-01-10
:Last Name: SAMPLE
:First Name: JELANI
:Address: 123 MAIN STREET PHOENIX AZ 85007
:Date Of Birth: 1957-02-01
:Restrictions: NONE
:Endorsements: NONE
:Driver License Class: D
:Sex: M
:Height: 5-08
:Weight: 185
:Hair Color: BRO
:Eye Color: BRO
:Document Discriminator: 1234567890123456

Page Predictions
================

Page 0
------
:Photo: Polygon with 4 points.
:Signature: Polygon with 4 points.
:State: AZ
:Driver License ID: D12345678
:Expiry Date: 2018-02-01
:Date Of Issue: 2013-01-10
:Last Name: SAMPLE
:First Name: JELANI
:Address: 123 MAIN STREET PHOENIX AZ 85007
:Date Of Birth: 1957-02-01
:Restrictions: NONE
:Endorsements: NONE
:Driver License Class: D
:Sex: M
:Height: 5-08
:Weight: 185
:Hair Color: BRO
:Eye Color: BRO
:Document Discriminator: 1234567890123456
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
**address** : US driver license holders address

```php
echo $result->document->inference->prediction->address->value;
```

## Date Of Birth
**dateOfBirth** : US driver license holders date of birth

```php
echo $result->document->inference->prediction->dateOfBirth->value;
```

## Document Discriminator
**ddNumber** : Document Discriminator Number of the US Driver License

```php
echo $result->document->inference->prediction->ddNumber->value;
```

## Driver License Class
**dlClass** : US driver license holders class

```php
echo $result->document->inference->prediction->dlClass->value;
```

## Driver License ID
**driverLicenseId** : ID number of the US Driver License.

```php
echo $result->document->inference->prediction->driverLicenseId->value;
```

## Endorsements
**endorsements** : US driver license holders endorsements

```php
echo $result->document->inference->prediction->endorsements->value;
```

## Expiry Date
**expiryDate** : Date on which the documents expires.

```php
echo $result->document->inference->prediction->expiryDate->value;
```

## Eye Color
**eyeColor** : US driver license holders eye colour

```php
echo $result->document->inference->prediction->eyeColor->value;
```

## First Name
**firstName** : US driver license holders first name(s)

```php
echo $result->document->inference->prediction->firstName->value;
```

## Hair Color
**hairColor** : US driver license holders hair colour

```php
echo $result->document->inference->prediction->hairColor->value;
```

## Height
**height** : US driver license holders hight

```php
echo $result->document->inference->prediction->height->value;
```

## Date Of Issue
**issuedDate** : Date on which the documents was issued.

```php
echo $result->document->inference->prediction->issuedDate->value;
```

## Last Name
**lastName** : US driver license holders last name

```php
echo $result->document->inference->prediction->lastName->value;
```

## Photo
[📄](#page-level-fields "This field is only present on individual pages.")**photo** : Has a photo of the US driver license holder

```php
foreach($result->document->photo as $photoElem){
    echo $photoElem;
}->polygon->getCoordinates();
```

## Restrictions
**restrictions** : US driver license holders restrictions

```php
echo $result->document->inference->prediction->restrictions->value;
```

## Sex
**sex** : US driver license holders gender

```php
echo $result->document->inference->prediction->sex->value;
```

## Signature
[📄](#page-level-fields "This field is only present on individual pages.")**signature** : Has a signature of the US driver license holder

```php
foreach($result->document->signature as $signatureElem){
    echo $signatureElem;
}->polygon->getCoordinates();
```

## State
**state** : US State

```php
echo $result->document->inference->prediction->state->value;
```

## Weight
**weight** : US driver license holders weight

```php
echo $result->document->inference->prediction->weight->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
