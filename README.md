[![License: MIT](https://img.shields.io/github/license/mindee/mindee-api-php)](https://opensource.org/licenses/MIT) [![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/mindee/mindee-api-php/unit-test.yml)](https://github.com/mindee/mindee-api-php) [![Packagist Version](https://img.shields.io/packagist/v/mindee/mindee)](https://packagist.org/packages/mindee/mindee) [![Downloads](https://img.shields.io/packagist/dm/mindee/mindee)](https://packagist.org/packages/mindee/mindee)

# Mindee API Helper Library for PHP
Quickly and easily connect to Mindee's API services using PHP.

## Quick Start
Here's the TL;DR of getting started.

First, get an [API Key](https://developers.mindee.com/docs/create-api-key)

If you do not have them, you'll need the following packages on your system:
* [php-curl](https://www.php.net/manual/en/curl.installation.php)
* [php-json](https://www.php.net/manual/en/json.installation.php) (not necessary for versions >= 8.0.0)
* [php-fileinfo](https://www.php.net/manual/en/fileinfo.installation.php)

Then, install this library:
```shell
composer require mindee/mindee
```

Finally, PHP away!

### Loading a File and Parsing It

#### Global Documents
```php
<?php

use Mindee\Client;
use Mindee\Product\Invoice\InvoiceV4;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(InvoiceV4::class, $inputSource);

// Print a brief summary of the parsed data
echo strval($apiResponse->document);
```

**Note:** Files can also be loaded from:

A PHP `File` compatible file:
```php
$inputDoc = $mindeeClient->sourceFromFile($myFile);
```

A URL (`HTTPS` only):
```php
$inputDoc = $mindeeClient->sourceFromUrl("https://files.readme.io/a74eaa5-c8e283b-sample_invoice.jpeg");
```

A base64-encoded string, making sure to specify the extension of the file name:
```php
$inputDoc = $mindeeClient->sourceFromB64($myInputString, "my-file-name.ext");
```

Raw bytes, making sure to specify the extension of the file name:
```php
$inputDoc = $mindeeClient->sourceFromBytes($myRawBytesSequence, "my-file-name.ext");
```

#### Region-Specific Documents
```php
use Mindee\Client;
use Mindee\Product\Us\BankCheck\BankCheckV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(BankCheckV1::class, $inputSource);

// Print a brief summary of the parsed data
echo strval($apiResponse->document);
```

#### Custom Document (API Builder)

```php
use Mindee\Client;
use Mindee\Product\Us\BankCheck\BankCheckV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(BankCheckV1::class, $inputSource);

// Print a brief summary of the parsed data
echo strval($apiResponse->document);
```

## Further Reading
Complete details on the working of the library are available in the following guides:

* [Getting started](https://developers.mindee.com/docs/php-getting-started)
* [PHP Command Line Interface (CLI)](https://developers.mindee.com/docs/php-cli)
* [PHP Custom APIs (API Builder)](https://developers.mindee.com/docs/php-api-builder)
* [PHP Generated APIs](https://developers.mindee.com/docs/php-generated-api)
* [PHP Invoice OCR](https://developers.mindee.com/docs/php-invoice-ocr)
* [PHP Receipt OCR](https://developers.mindee.com/docs/php-receipt-ocr)
* [PHP Financial Document OCR](https://developers.mindee.com/docs/php-financial-document-ocr)
* [PHP Passport OCR](https://developers.mindee.com/docs/php-passport-ocr)
* [PHP Resume OCR](https://developers.mindee.com/docs/php-resume-ocr)
* [PHP Proof of Address OCR](https://developers.mindee.com/docs/php-proof-of-address-ocr)
* [PHP International Id OCR](https://developers.mindee.com/docs/php-international-id-ocr)
* [PHP EU License Plate OCR](https://developers.mindee.com/docs/php-eu-license-plate-ocr)
* [PHP EU Driver License OCR](https://developers.mindee.com/docs/php-eu-driver-license-ocr)
* [PHP FR Bank Account Detail OCR](https://developers.mindee.com/docs/php-fr-bank-account-details-ocr)
* [PHP FR Carte Grise OCR](https://developers.mindee.com/docs/php-fr-carte-grise-ocr)
* [PHP FR Carte Vitale OCR](https://developers.mindee.com/docs/php-fr-carte-vitale-ocr)
* [PHP FR ID Card OCR](https://developers.mindee.com/docs/php-fr-id-card-ocr)
* [PHP US Bank Check OCR](https://developers.mindee.com/docs/php-us-bank-checks-ocr)
* [PHP US W9 OCR](https://developers.mindee.com/docs/php-us-w9-ocr)
* [PHP US Driver License OCR](https://developers.mindee.com/docs/php-us-driver-license-ocr)
* [PHP Barcode Reader API](https://developers.mindee.com/docs/php-barcode-reader-api)
* [PHP Cropper API](https://developers.mindee.com/docs/php-cropper-api)
* [PHP Invoice Splitter API](https://developers.mindee.com/docs/php-invoice-splitter-api)
* [PHP Multi Receipts Detector API](https://developers.mindee.com/docs/php-multi-receipts-detector-api)

You can view the source code on [GitHub](https://github.com/mindee/mindee-api-php).

You can also take a look at the
**[Reference Documentation](https://mindee.github.io/mindee-api-php/)**.

## License
Copyright © Mindee

Available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).

## Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
