---
title: Financial Document OCR PHP
---
The PHP OCR SDK supports the [Financial Document API](https://platform.mindee.com/mindee/financial_document).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/financial_document/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Financial Document sample](https://github.com/mindee/client-lib-test-data/blob/main/products/financial_document/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\FinancialDocument\FinancialDocumentV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(FinancialDocumentV1::class, $inputSource);

echo $apiResponse->document;
```

You can also call this product asynchronously:

```php
<?php

use Mindee\Client;
use Mindee\Product\FinancialDocument\FinancialDocumentV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(FinancialDocumentV1::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 3859a462-e05f-4f4c-a736-febca66b9aa9
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/financial_document v1.9
:Rotation applied: Yes

Prediction
==========
:Locale: en; en; USD;
:Invoice Number: INT-001
:Receipt Number:
:Document Number: INT-001
:Reference Numbers: 2412/2019
:Purchase Date: 2019-11-02
:Due Date: 2019-02-26
:Total Net: 195.00
:Total Amount: 204.75
:Taxes:
  +---------------+--------+----------+---------------+
  | Base          | Code   | Rate (%) | Amount        |
  +===============+========+==========+===============+
  |               |        | 5.00     | 9.75          |
  +---------------+--------+----------+---------------+
:Supplier Payment Details:
:Supplier Name: JOHN SMITH
:Supplier Company Registrations:
:Supplier Address: 4490 Oak Drive Albany, NY 12210
:Supplier Phone Number:
:Customer Name: JESSIE M HORNE
:Supplier Website:
:Supplier Email:
:Customer Company Registrations:
:Customer Address: 2019 Redbud Drive New York, NY 10011
:Customer ID: 1234567890
:Shipping Address: 2019 Redbud Drive New York, NY 10011
:Billing Address: 4312 Wood Road New York, NY 10031
:Document Type: INVOICE
:Purchase Subcategory:
:Purchase Category: miscellaneous
:Total Tax: 9.75
:Tip and Gratuity:
:Purchase Time:
:Line Items:
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | Description                          | Product code | Quantity | Tax Amount | Tax Rate (%) | Total Amount | Unit of measure | Unit Price |
  +======================================+==============+==========+============+==============+==============+=================+============+
  | Front and rear brake cables          |              | 1.00     |            |              | 100.00       |                 | 100.00     |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | New set of pedal arms                |              | 2.00     |            |              | 50.00        |                 | 25.00      |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | Labor 3hrs                           |              | 3.00     |            |              | 45.00        |                 | 15.00      |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+

Page Predictions
================

Page 0
------
:Locale: en; en; USD;
:Invoice Number: INT-001
:Receipt Number:
:Document Number: INT-001
:Reference Numbers: 2412/2019
:Purchase Date: 2019-11-02
:Due Date: 2019-02-26
:Total Net: 195.00
:Total Amount: 204.75
:Taxes:
  +---------------+--------+----------+---------------+
  | Base          | Code   | Rate (%) | Amount        |
  +===============+========+==========+===============+
  |               |        | 5.00     | 9.75          |
  +---------------+--------+----------+---------------+
:Supplier Payment Details:
:Supplier Name: JOHN SMITH
:Supplier Company Registrations:
:Supplier Address: 4490 Oak Drive Albany, NY 12210
:Supplier Phone Number:
:Customer Name: JESSIE M HORNE
:Supplier Website:
:Supplier Email:
:Customer Company Registrations:
:Customer Address: 2019 Redbud Drive New York, NY 10011
:Customer ID: 1234567890
:Shipping Address: 2019 Redbud Drive New York, NY 10011
:Billing Address: 4312 Wood Road New York, NY 10031
:Document Type: INVOICE
:Purchase Subcategory:
:Purchase Category: miscellaneous
:Total Tax: 9.75
:Tip and Gratuity:
:Purchase Time:
:Line Items:
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | Description                          | Product code | Quantity | Tax Amount | Tax Rate (%) | Total Amount | Unit of measure | Unit Price |
  +======================================+==============+==========+============+==============+==============+=================+============+
  | Front and rear brake cables          |              | 1.00     |            |              | 100.00       |                 | 100.00     |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | New set of pedal arms                |              | 2.00     |            |              | 50.00        |                 | 25.00      |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
  | Labor 3hrs                           |              | 3.00     |            |              | 45.00        |                 | 15.00      |
  +--------------------------------------+--------------+----------+------------+--------------+--------------+-----------------+------------+
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


### AmountField
The amount field `AmountField` only has one constraint: its **value** is an optional `?float`.


### ClassificationField
The classification field `ClassificationField` does not implement all the basic `BaseField` attributes. It only implements **value**, **confidence** and **pageId**.

> Note: a classification field's `value is always a `string`.


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

### PaymentDetailsField
Aside from the basic `BaseField` attributes, the payment details field `PaymentDetailsField` also implements the following:

* **accountNumber** (`string`): number of an account, expressed as a string. Can be `null`.
* **iban** (`string`): International Bank Account Number. Can be `null`.
* **routingNumber** (`string`): routing number of an account. Can be `null`.
* **swift** (`string`): the account holder's bank's SWIFT Business Identifier Code (BIC). Can be `null`.

### StringField
The text field `StringField` only has one constraint: its **value** is an optional `?string`.

### TaxesField
#### Tax
Aside from the basic `BaseField` attributes, the tax field `TaxField` also implements the following:

* **rate** (`float`): the tax rate applied to an item expressed as a percentage. Can be `null`.
* **code** (`string`): tax code (or equivalent, depending on the origin of the document). Can be `null`.
* **base** (`float`): base amount used for the tax. Can be `null`.

> Note: currently `TaxField` is not used on its own, and is accessed through a parent `Taxes` object, a list-like structure.

#### Taxes (Array)
The `Taxes` field represents a list-like collection of `TaxField` objects. As it is the representation of several objects, it has access to a custom `__toString` method that can render a `TaxField` object as a table line.

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Line Items Field
List of line item details.

A `FinancialDocumentV1LineItem` implements the following attributes:

* **description** (`string`): The item description.
* **productCode** (`string`): The product code referring to the item.
* **quantity** (`float`): The item quantity
* **taxAmount** (`float`): The item tax amount.
* **taxRate** (`float`): The item tax rate in percentage.
* **totalAmount** (`float`): The item total amount.
* **unitMeasure** (`string`): The item unit of measure.
* **unitPrice** (`float`): The item unit price.

# Attributes
The following fields are extracted for Financial Document V1:

## Billing Address
**billingAddress** : The customer's address used for billing.

```php
echo $result->document->inference->prediction->billingAddress->value;
```

## Purchase Category
**category** : The purchase category among predefined classes.

```php
echo $result->document->inference->prediction->category->value;
```

## Customer Address
**customerAddress** : The address of the customer.

```php
echo $result->document->inference->prediction->customerAddress->value;
```

## Customer Company Registrations
**customerCompanyRegistrations** : List of company registrations associated to the customer.

```php
foreach ($result->document->inference->prediction->customerCompanyRegistrations as $customerCompanyRegistrationsElem)
{
    echo $customerCompanyRegistrationsElem->value;
}
```

## Customer ID
**customerId** : The customer account number or identifier from the supplier.

```php
echo $result->document->inference->prediction->customerId->value;
```

## Customer Name
**customerName** : The name of the customer.

```php
echo $result->document->inference->prediction->customerName->value;
```

## Purchase Date
**date** : The date the purchase was made.

```php
echo $result->document->inference->prediction->date->value;
```

## Document Number
**documentNumber** : The document number or identifier.

```php
echo $result->document->inference->prediction->documentNumber->value;
```

## Document Type
**documentType** : One of: 'INVOICE', 'CREDIT NOTE', 'CREDIT CARD RECEIPT', 'EXPENSE RECEIPT'.

```php
echo $result->document->inference->prediction->documentType->value;
```

## Due Date
**dueDate** : The date on which the payment is due.

```php
echo $result->document->inference->prediction->dueDate->value;
```

## Invoice Number
**invoiceNumber** : The invoice number or identifier only if document is an invoice.

```php
echo $result->document->inference->prediction->invoiceNumber->value;
```

## Line Items
**lineItems** (List[[FinancialDocumentV1LineItem](#line-items-field)]): List of line item details.

```php
foreach ($result->document->inference->prediction->lineItems as $lineItemsElem)
{
    echo $lineItemsElem->value;
}
```

## Locale
**locale** : The locale detected on the document.

```php
echo $result->document->inference->prediction->locale->value;
```

## Receipt Number
**receiptNumber** : The receipt number or identifier only if document is a receipt.

```php
echo $result->document->inference->prediction->receiptNumber->value;
```

## Reference Numbers
**referenceNumbers** : List of Reference numbers, including PO number.

```php
foreach ($result->document->inference->prediction->referenceNumbers as $referenceNumbersElem)
{
    echo $referenceNumbersElem->value;
}
```

## Shipping Address
**shippingAddress** : The customer's address used for shipping.

```php
echo $result->document->inference->prediction->shippingAddress->value;
```

## Purchase Subcategory
**subcategory** : The purchase subcategory among predefined classes for transport and food.

```php
echo $result->document->inference->prediction->subcategory->value;
```

## Supplier Address
**supplierAddress** : The address of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierAddress->value;
```

## Supplier Company Registrations
**supplierCompanyRegistrations** : List of company registrations associated to the supplier.

```php
foreach ($result->document->inference->prediction->supplierCompanyRegistrations as $supplierCompanyRegistrationsElem)
{
    echo $supplierCompanyRegistrationsElem->value;
}
```

## Supplier Email
**supplierEmail** : The email of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierEmail->value;
```

## Supplier Name
**supplierName** : The name of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierName->value;
```

## Supplier Payment Details
**supplierPaymentDetails** : List of payment details associated to the supplier.

```php
foreach ($result->document->inference->prediction->supplierPaymentDetails as $supplierPaymentDetailsElem)
{
    echo $supplierPaymentDetailsElem->value;
    echo $supplierPaymentDetailsElem->rate;
    echo $supplierPaymentDetailsElem->code;
    echo $supplierPaymentDetailsElem->basis;
}
```

## Supplier Phone Number
**supplierPhoneNumber** : The phone number of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierPhoneNumber->value;
```

## Supplier Website
**supplierWebsite** : The website URL of the supplier or merchant.

```php
echo $result->document->inference->prediction->supplierWebsite->value;
```

## Taxes
**taxes** : List of tax lines information.

```php
foreach ($result->document->inference->prediction->taxes as $taxesElem)
{
    echo $taxesElem->value;
}
```

## Purchase Time
**time** : The time the purchase was made.

```php
echo $result->document->inference->prediction->time->value;
```

## Tip and Gratuity
**tip** : The total amount of tip and gratuity

```php
echo $result->document->inference->prediction->tip->value;
```

## Total Amount
**totalAmount** : The total amount paid: includes taxes, tips, fees, and other charges.

```php
echo $result->document->inference->prediction->totalAmount->value;
```

## Total Net
**totalNet** : The net amount paid: does not include taxes, fees, and discounts.

```php
echo $result->document->inference->prediction->totalNet->value;
```

## Total Tax
**totalTax** : The total amount of taxes.

```php
echo $result->document->inference->prediction->totalTax->value;
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
