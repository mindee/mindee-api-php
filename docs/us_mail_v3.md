---
title: US US Mail OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-us-us-mail-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [US Mail API](https://platform.mindee.com/mindee/us_mail).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/us_mail/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![US Mail sample](https://github.com/mindee/client-lib-test-data/blob/main/products/us_mail/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Us\UsMail\UsMailV3;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(UsMailV3::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: f9c36f59-977d-4ddc-9f2d-31c294c456ac
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/us_mail v3.0
:Rotation applied: Yes

Prediction
==========
:Sender Name: company zed
:Sender Address:
  :City: Dallas
  :Complete Address: 54321 Elm Street, Dallas, Texas 54321
  :Postal Code: 54321
  :State: TX
  :Street: 54321 Elm Street
:Recipient Names: Jane Doe
:Recipient Addresses:
  +-----------------+-------------------------------------+-------------------+-------------+------------------------+-------+---------------------------+-----------------+
  | City            | Complete Address                    | Is Address Change | Postal Code | Private Mailbox Number | State | Street                    | Unit            |
  +=================+=====================================+===================+=============+========================+=======+===========================+=================+
  | Detroit         | 1234 Market Street PMB 4321, Det... | False             | 12345       | 4321                   | MI    | 1234 Market Street        |                 |
  +-----------------+-------------------------------------+-------------------+-------------+------------------------+-------+---------------------------+-----------------+
:Return to Sender: False
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

### BooleanField
The boolean field `BooleanField` only has one constraint: its **value** is an optional `?bool`.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Recipient Addresses Field
The addresses of the recipients.

A `UsMailV3RecipientAddress` implements the following attributes:

* **city** (`string`): The city of the recipient's address.
* **complete** (`string`): The complete address of the recipient.
* **isAddressChange** (`bool`): Indicates if the recipient's address is a change of address.
* **postalCode** (`string`): The postal code of the recipient's address.
* **privateMailboxNumber** (`string`): The private mailbox number of the recipient's address.
* **state** (`string`): Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
* **street** (`string`): The street of the recipient's address.
* **unit** (`string`): The unit number of the recipient's address.
Fields which are specific to this product; they are not used in any other product.

### Sender Address Field
The address of the sender.

A `UsMailV3SenderAddress` implements the following attributes:

* **city** (`string`): The city of the sender's address.
* **complete** (`string`): The complete address of the sender.
* **postalCode** (`string`): The postal code of the sender's address.
* **state** (`string`): Second part of the ISO 3166-2 code, consisting of two letters indicating the US State.
* **street** (`string`): The street of the sender's address.

# Attributes
The following fields are extracted for US Mail V3:

## Return to Sender
**isReturnToSender** : Whether the mailing is marked as return to sender.

```php
echo $result->document->inference->prediction->isReturnToSender->value;
```

## Recipient Addresses
**recipientAddresses** ([[UsMailV3RecipientAddress](#recipient-addresses-field)]): The addresses of the recipients.

```php
foreach ($result->document->inference->prediction->recipientAddresses as $recipientAddressesElem)
{
    echo $recipientAddressesElem->value;
}
```

## Recipient Names
**recipientNames** : The names of the recipients.

```php
foreach ($result->document->inference->prediction->recipientNames as $recipientNamesElem)
{
    echo $recipientNamesElem->value;
}
```

## Sender Address
**senderAddress** ([UsMailV3SenderAddress](#sender-address-field)): The address of the sender.

```php
echo $result->document->inference->prediction->senderAddress->value;
```

## Sender Name
**senderName** : The name of the sender.

```php
echo $result->document->inference->prediction->senderName->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
