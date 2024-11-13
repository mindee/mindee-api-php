---
title: Business Card OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-business-card-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Business Card API](https://platform.mindee.com/mindee/business_card).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/business_card/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Business Card sample](https://github.com/mindee/client-lib-test-data/blob/main/products/business_card/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\BusinessCard\BusinessCardV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(BusinessCardV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 6f9a261f-7609-4687-9af0-46a45156566e
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/business_card v1.0
:Rotation applied: Yes

Prediction
==========
:Firstname: Andrew
:Lastname: Morin
:Job Title: Founder & CEO
:Company: RemoteGlobal
:Email: amorin@remoteglobalconsulting.com
:Phone Number: +14015555555
:Mobile Number: +13015555555
:Fax Number: +14015555556
:Address: 178 Main Avenue, Providence, RI 02111
:Website: www.remoteglobalconsulting.com
:Social Media: https://www.linkedin.com/in/johndoe
               https://twitter.com/johndoe
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

### StringField
The text field `StringField` implements the following:
* **value** (`string`): represents the value of the field as a string.
* **rawValue** (`string`): the value of the string as it appears on the document.

# Attributes
The following fields are extracted for Business Card V1:

## Address
**address** : The address of the person.

```php
echo $result->document->inference->prediction->address->value;
```

## Company
**company** : The company the person works for.

```php
echo $result->document->inference->prediction->company->value;
```

## Email
**email** : The email address of the person.

```php
echo $result->document->inference->prediction->email->value;
```

## Fax Number
**faxNumber** : The Fax number of the person.

```php
echo $result->document->inference->prediction->faxNumber->value;
```

## Firstname
**firstname** : The given name of the person.

```php
echo $result->document->inference->prediction->firstname->value;
```

## Job Title
**jobTitle** : The job title of the person.

```php
echo $result->document->inference->prediction->jobTitle->value;
```

## Lastname
**lastname** : The lastname of the person.

```php
echo $result->document->inference->prediction->lastname->value;
```

## Mobile Number
**mobileNumber** : The mobile number of the person.

```php
echo $result->document->inference->prediction->mobileNumber->value;
```

## Phone Number
**phoneNumber** : The phone number of the person.

```php
echo $result->document->inference->prediction->phoneNumber->value;
```

## Social Media
**socialMedia** : The social media profiles of the person or company.

```php
foreach ($result->document->inference->prediction->socialMedia as $socialMediaElem)
{
    echo $socialMediaElem->value;
}
```

## Website
**website** : The website of the person or company.

```php
echo $result->document->inference->prediction->website->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
