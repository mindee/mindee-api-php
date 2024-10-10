---
title: FR Payslip OCR PHP
category: 622b805aaec68102ea7fcbc2
slug: php-fr-payslip-ocr
parentDoc: 658193df8e029d002ad9c89b
---
The PHP OCR SDK supports the [Payslip API](https://platform.mindee.com/mindee/payslip_fra).

Using the [sample below](https://github.com/mindee/client-lib-test-data/blob/main/products/payslip_fra/default_sample.jpg), we are going to illustrate how to extract the data that we want using the OCR SDK.
![Payslip sample](https://github.com/mindee/client-lib-test-data/blob/main/products/payslip_fra/default_sample.jpg?raw=true)

# Quick-Start
```php
<?php

use Mindee\Client;
use Mindee\Product\Fr\Payslip\PayslipV2;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file asynchronously
$apiResponse = $mindeeClient->enqueueAndParse(PayslipV2::class, $inputSource);

echo $apiResponse->document;
```

**Output (RST):**
```rst
########
Document
########
:Mindee ID: 972edba5-25aa-49d0-8431-e2557ddd788e
:Filename: default_sample.jpg

Inference
#########
:Product: mindee/payslip_fra v2.0
:Rotation applied: No

Prediction
==========
:Employee:
  :Address: 52 RUE DES FLEURS 33500 LIBOURNE FRANCE
  :Date of Birth:
  :First Name: Jean Luc
  :Last Name: Picard
  :Phone Number:
  :Registration Number:
  :Social Security Number: 123456789012345
:Employer:
  :Address: 1 RUE DU TONNOT 25210 DOUBS
  :Company ID: 12345678901234
  :Company Site:
  :NAF Code: 1234A
  :Name: DEMO COMPANY
  :Phone Number:
  :URSSAF Number:
:Bank Account Details:
  :Bank Name:
  :IBAN:
  :SWIFT:
:Employment:
  :Category: Cadre
  :Coefficient: 600.00
  :Collective Agreement: Construction -- Promotion
  :Job Title: Directeur Régional du Développement
  :Position Level:
  :Start Date: 2022-05-01
:Salary Details:
  +--------------+-----------+--------------------------------------+-----------+
  | Amount       | Base      | Description                          | Rate      |
  +==============+===========+======================================+===========+
  | 6666.67      |           | Salaire de base                      |           |
  +--------------+-----------+--------------------------------------+-----------+
  | 9.30         |           | Part patronale Mutuelle NR           |           |
  +--------------+-----------+--------------------------------------+-----------+
  | 508.30       |           | Avantages en nature voiture          |           |
  +--------------+-----------+--------------------------------------+-----------+
:Pay Detail:
  :Gross Salary: 7184.27
  :Gross Salary YTD: 18074.81
  :Income Tax Rate: 17.60
  :Income Tax Withheld: 1030.99
  :Net Paid: 3868.32
  :Net Paid Before Tax: 4899.31
  :Net Taxable: 5857.90
  :Net Taxable YTD: 14752.73
  :Total Cost Employer: 10486.94
  :Total Taxes and Deductions: 1650.36
:PTO:
  :Accrued This Period: 6.17
  :Balance End of Period: 6.17
  :Used This Period:
:Pay Period:
  :End Date: 2023-03-31
  :Month: 03
  :Payment Date: 2023-03-29
  :Start Date: 2023-03-01
  :Year: 2023
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

## Specific Fields
Fields which are specific to this product; they are not used in any other product.

### Bank Account Details Field
Information about the employee's bank account.

A `PayslipV2BankAccountDetail` implements the following attributes:

* **bankName** (`string`): The name of the bank.
* **iban** (`string`): The IBAN of the bank account.
* **swift** (`string`): The SWIFT code of the bank.
Fields which are specific to this product; they are not used in any other product.

### Employee Field
Information about the employee.

A `PayslipV2Employee` implements the following attributes:

* **address** (`string`): The address of the employee.
* **dateOfBirth** (`string`): The date of birth of the employee.
* **firstName** (`string`): The first name of the employee.
* **lastName** (`string`): The last name of the employee.
* **phoneNumber** (`string`): The phone number of the employee.
* **registrationNumber** (`string`): The registration number of the employee.
* **socialSecurityNumber** (`string`): The social security number of the employee.
Fields which are specific to this product; they are not used in any other product.

### Employer Field
Information about the employer.

A `PayslipV2Employer` implements the following attributes:

* **address** (`string`): The address of the employer.
* **companyId** (`string`): The company ID of the employer.
* **companySite** (`string`): The site of the company.
* **nafCode** (`string`): The NAF code of the employer.
* **name** (`string`): The name of the employer.
* **phoneNumber** (`string`): The phone number of the employer.
* **urssafNumber** (`string`): The URSSAF number of the employer.
Fields which are specific to this product; they are not used in any other product.

### Employment Field
Information about the employment.

A `PayslipV2Employment` implements the following attributes:

* **category** (`string`): The category of the employment.
* **coefficient** (`float`): The coefficient of the employment.
* **collectiveAgreement** (`string`): The collective agreement of the employment.
* **jobTitle** (`string`): The job title of the employee.
* **positionLevel** (`string`): The position level of the employment.
* **startDate** (`string`): The start date of the employment.
Fields which are specific to this product; they are not used in any other product.

### Pay Detail Field
Detailed information about the pay.

A `PayslipV2PayDetail` implements the following attributes:

* **grossSalary** (`float`): The gross salary of the employee.
* **grossSalaryYtd** (`float`): The year-to-date gross salary of the employee.
* **incomeTaxRate** (`float`): The income tax rate of the employee.
* **incomeTaxWithheld** (`float`): The income tax withheld from the employee's pay.
* **netPaid** (`float`): The net paid amount of the employee.
* **netPaidBeforeTax** (`float`): The net paid amount before tax of the employee.
* **netTaxable** (`float`): The net taxable amount of the employee.
* **netTaxableYtd** (`float`): The year-to-date net taxable amount of the employee.
* **totalCostEmployer** (`float`): The total cost to the employer.
* **totalTaxesAndDeductions** (`float`): The total taxes and deductions of the employee.
Fields which are specific to this product; they are not used in any other product.

### Pay Period Field
Information about the pay period.

A `PayslipV2PayPeriod` implements the following attributes:

* **endDate** (`string`): The end date of the pay period.
* **month** (`string`): The month of the pay period.
* **paymentDate** (`string`): The date of payment for the pay period.
* **startDate** (`string`): The start date of the pay period.
* **year** (`string`): The year of the pay period.
Fields which are specific to this product; they are not used in any other product.

### PTO Field
Information about paid time off.

A `PayslipV2Pto` implements the following attributes:

* **accruedThisPeriod** (`float`): The amount of paid time off accrued in this period.
* **balanceEndOfPeriod** (`float`): The balance of paid time off at the end of the period.
* **usedThisPeriod** (`float`): The amount of paid time off used in this period.
Fields which are specific to this product; they are not used in any other product.

### Salary Details Field
Detailed information about the earnings.

A `PayslipV2SalaryDetail` implements the following attributes:

* **amount** (`float`): The amount of the earnings.
* **base** (`float`): The base value of the earnings.
* **description** (`string`): The description of the earnings.
* **rate** (`float`): The rate of the earnings.

# Attributes
The following fields are extracted for Payslip V2:

## Bank Account Details
**bankAccountDetails** ([PayslipV2BankAccountDetail](#bank-account-details-field)): Information about the employee's bank account.

```php
echo $result->document->inference->prediction->bankAccountDetails->value;
```

## Employee
**employee** ([PayslipV2Employee](#employee-field)): Information about the employee.

```php
echo $result->document->inference->prediction->employee->value;
```

## Employer
**employer** ([PayslipV2Employer](#employer-field)): Information about the employer.

```php
echo $result->document->inference->prediction->employer->value;
```

## Employment
**employment** ([PayslipV2Employment](#employment-field)): Information about the employment.

```php
echo $result->document->inference->prediction->employment->value;
```

## Pay Detail
**payDetail** ([PayslipV2PayDetail](#pay-detail-field)): Detailed information about the pay.

```php
echo $result->document->inference->prediction->payDetail->value;
```

## Pay Period
**payPeriod** ([PayslipV2PayPeriod](#pay-period-field)): Information about the pay period.

```php
echo $result->document->inference->prediction->payPeriod->value;
```

## PTO
**pto** ([PayslipV2Pto](#pto-field)): Information about paid time off.

```php
echo $result->document->inference->prediction->pto->value;
```

## Salary Details
**salaryDetails** ([[PayslipV2SalaryDetail](#salary-details-field)]): Detailed information about the earnings.

```php
foreach ($result->document->inference->prediction->salaryDetails as $salaryDetailsElem)
{
    echo $salaryDetailsElem->value;
}
```

# Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
