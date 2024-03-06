---
title: Proof of Address OCR PHP
---
The PHP OCR SDK supports the [Proof of Address API](https://platform.mindee.com/mindee/proof_of_address).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/proof_of_address/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Proof of Address sample](https://github.com/mindee/client-lib-test-data/blob/main/products/proof_of_address/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\ProofOfAddress\ProofOfAddressV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(ProofOfAddressV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 5d2361e9-405e-4fc1-8531-f92a3aef0c38
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/proof_of_address v1.1
:Rotation applied: Yes

Prediction
==========
:Locale: en; en; USD;
:Issuer Name: PPL ELECTRIC UTILITIES
:Issuer Company Registrations:
:Issuer Address: 2 NORTH 9TH STREET CPC-GENN1 ALLENTOWN.PA 18101-1175
:Recipient Name:
:Recipient Company Registrations:
:Recipient Address: 123 MAIN ST ANYTOWN,PA 18062
:Dates: 2011-07-27
        2011-07-06
        2011-08-03
        2011-07-27
        2011-06-01
        2011-07-01
        2010-07-01
        2010-08-01
        2011-07-01
        2009-08-01
        2010-07-01
        2011-07-27
:Date of Issue: 2011-07-27

Page Predictions
================

Page 0
------
:Locale: en; en; USD;
:Issuer Name: PPL ELECTRIC UTILITIES
:Issuer Company Registrations:
:Issuer Address: 2 NORTH 9TH STREET CPC-GENN1 ALLENTOWN.PA 18101-1175
:Recipient Name:
:Recipient Company Registrations:
:Recipient Address: 123 MAIN ST ANYTOWN,PA 18062
:Dates: 2011-07-27
        2011-07-06
        2011-08-03
        2011-07-27
        2011-06-01
        2011-07-01
        2010-07-01
        2010-08-01
        2011-07-01
        2009-08-01
        2010-07-01
        2011-07-27
:Date of Issue: 2011-07-27
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


### CompanyRegistrationField
Aside from the basic `BaseField` attributes, the company registration field `CompanyRegistrationField` also implements the following:

* **type** (`string`): the type of company.

### DateField
Aside from the basic `BaseField` attributes, the date field `DateField` also implements the following: 

* **dateObject** (`date`): an accessible representation of the value as a php object. Can be `null`.

### LocaleField
The locale field `LocaleField` only implements the **value**, **confidence** and **pageId** base `BaseField` attributes, but it comes with its own:

* **language** (`string`): ISO 639-1 language code (e.g.: `en` for English). Can be `null`.
* **country** (`string`): ISO 3166-1 alpha-2 or ISO 3166-1 alpha-3 code for countries (e.g.: `GRB` or `GB` for "Great Britain"). Can be `null`.
* **currency** (`string`): ISO 4217 code for currencies (e.g.: `USD` for "US Dollars"). Can be `null`.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

# Attributes
The following fields are extracted for Proof of Address V1:

## Date of Issue
**date** : The date the document was issued.

```php
echo $result->document->inference->prediction->date->value;
```

## Dates
**dates** : List of dates found on the document.

```php
foreach ($result->document->inference->prediction->dates as $datesElem)
{
    echo $datesElem->value;
}
```

## Issuer Address
**issuerAddress** : The address of the document's issuer.

```php
echo $result->document->inference->prediction->issuerAddress->value;
```

## Issuer Company Registrations
**issuerCompanyRegistration** : List of company registrations found for the issuer.

```php
foreach ($result->document->inference->prediction->issuerCompanyRegistration as $issuerCompanyRegistrationElem)
{
    echo $issuerCompanyRegistrationElem->value;
}
```

## Issuer Name
**issuerName** : The name of the person or company issuing the document.

```php
echo $result->document->inference->prediction->issuerName->value;
```

## Locale
**locale** : The locale detected on the document.

```php
echo $result->document->inference->prediction->locale->value;
```

## Recipient Address
**recipientAddress** : The address of the recipient.

```php
echo $result->document->inference->prediction->recipientAddress->value;
```

## Recipient Company Registrations
**recipientCompanyRegistration** : List of company registrations found for the recipient.

```php
foreach ($result->document->inference->prediction->recipientCompanyRegistration as $recipientCompanyRegistrationElem)
{
    echo $recipientCompanyRegistrationElem->value;
}
```

## Recipient Name
**recipientName** : The name of the person or company receiving the document.

```php
echo $result->document->inference->prediction->recipientName->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
