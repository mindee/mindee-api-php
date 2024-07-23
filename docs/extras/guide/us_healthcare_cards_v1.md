---
title: US Healthcare Card OCR PHP
---
The PHP OCR SDK supports the [Healthcare Card API](https://platform.mindee.com/mindee/us_healthcare_cards).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/us_healthcare_cards/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Healthcare Card sample](https://github.com/mindee/client-lib-test-data/blob/main/products/us_healthcare_cards/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Us\HealthcareCard\HealthcareCardV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(HealthcareCardV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 0ced9f49-00c0-4a1d-8221-4a1538813a95
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/us_healthcare_cards v1.0
:Rotation applied: No

Prediction
==========
:Company Name: UnitedHealthcare
:Member Name: SUBSCRIBER SMITH
:Member ID: 123456789
:Issuer 80840:
:Dependents: SPOUSE SMITH
             CHILD1 SMITH
             CHILD2 SMITH
             CHILD3 SMITH
:Group Number: 98765
:Payer ID: 87726
:RX BIN: 610279
:RX GRP: UHEALTH
:RX PCN: 9999
:copays:
  +--------------+--------------+
  | Service Fees | Service Name |
  +==============+==============+
  | 20.00        | office visit |
  +--------------+--------------+
  | 300.00       | emergency    |
  +--------------+--------------+
  | 75.00        | urgent care  |
  +--------------+--------------+
  | 30.00        | specialist   |
  +--------------+--------------+
:Enrollment Date: 2023-09-13
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

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### copays Field
Is a fixed amount for a covered service.

A `HealthcareCardV1Copay` implements the following attributes:

* **serviceFees** (`float`): The price of service.
* **serviceName** (`string`): The name of service of the copay.

# Attributes
The following fields are extracted for Healthcare Card V1:

## Company Name
**companyName** : The name of the company that provides the healthcare plan.

```php
echo $result->document->inference->prediction->companyName->value;
```

## copays
**copays** (List[[HealthcareCardV1Copay](#copays-field)]): Is a fixed amount for a covered service.

```php
foreach ($result->document->inference->prediction->copays as $copaysElem)
{
    echo $copaysElem->value;
}
```

## Dependents
**dependents** : The list of dependents covered by the healthcare plan.

```php
foreach ($result->document->inference->prediction->dependents as $dependentsElem)
{
    echo $dependentsElem->value;
}
```

## Enrollment Date
**enrollmentDate** : The date when the member enrolled in the healthcare plan.

```php
echo $result->document->inference->prediction->enrollmentDate->value;
```

## Group Number
**groupNumber** : The group number associated with the healthcare plan.

```php
echo $result->document->inference->prediction->groupNumber->value;
```

## Issuer 80840
**issuer80840** : The organization that issued the healthcare plan.

```php
echo $result->document->inference->prediction->issuer80840->value;
```

## Member ID
**memberId** : The unique identifier for the member in the healthcare system.

```php
echo $result->document->inference->prediction->memberId->value;
```

## Member Name
**memberName** : The name of the member covered by the healthcare plan.

```php
echo $result->document->inference->prediction->memberName->value;
```

## Payer ID
**payerId** : The unique identifier for the payer in the healthcare system.

```php
echo $result->document->inference->prediction->payerId->value;
```

## RX BIN
**rxBin** : The BIN number for prescription drug coverage.

```php
echo $result->document->inference->prediction->rxBin->value;
```

## RX GRP
**rxGrp** : The group number for prescription drug coverage.

```php
echo $result->document->inference->prediction->rxGrp->value;
```

## RX PCN
**rxPcn** : The PCN number for prescription drug coverage.

```php
echo $result->document->inference->prediction->rxPcn->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
