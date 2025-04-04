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

#### Custom Documents (docTI & Custom APIs)

```php
use Mindee\Client;
use Mindee\Product\Generated\GeneratedV1;

// Init a new client
$mindeeClient = new Client("my-api-key");

// Load a file from disk
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");

// Parse the file
$apiResponse = $mindeeClient->parse(GeneratedV1::class, $inputSource);

// Print a brief summary of the parsed data
echo strval($apiResponse->document);
```

## Full PDF support

Some features such as Invoice Splitter auto-extraction & Multi Receipts auto-extraction require the [ImageMagick](https://www.php.net/manual/en/imagick.setup.php) library, which in turn requires [GhostScript](https://www.ghostscript.com/).

### Unix

ImageMagick is usually bundled with most installations. If not, you can install it using your distribution's package manager.

More details [here](https://imagemagick.org/script/advanced-linux-installation.php).

Ghostscript can be installed from the [website download page](https://ghostscript.com/releases/gsdnld.html), or if using an apt compatible distribution:
```bash
sudo apt-get update
sudo apt-get install -y ghostscript
```

In some cases, you might also want to authorize the ImageMagick policy to edit PDF files:

```bash
DQT='"'
SRC="rights=${DQT}none${DQT} pattern=${DQT}PDF${DQT}"
RPL="rights=${DQT}read|write${DQT} pattern=${DQT}PDF${DQT}"
sudo sed -i "s/$SRC/$RPL/" /etc/ImageMagick-6/policy.xml
```

### MacOS

Brew will be required for this install.

```bash
brew update
brew install imagemagick
pecl install imagick # Might not be needed in some instances. Do not try to install before installing PHP & Pecl.
```

### Windows
You can install [Ghostscript](https://ghostscript.com/releases/gsdnld.html) by downloading it, or simply by using [Chocolatey](https://chocolatey.org/).

```
choco install ghostscript --version 10.03.1 -y
```

**⚠️ Important note if you are using Windows** 
The `gs` alias might not be available by default, but it is possible to bind it fairly simply by either adding `gswin32c.exe` or `gswin64c.exe` to your `$PATH` and then adding a symlink in powershell using:

```
New-Item -ItemType SymbolicLink -Path "C:\Windows\gs.exe" -Target "C:\Program Files\gs\gs10.03.1\bin\gswin64c.exe"
New-Item -ItemType SymbolicLink -Path "C:\Windows\gs" -Target "C:\Program Files\gs\gs10.03.1\bin\gswin64c.exe"
```
## Further Reading
Complete details on the working of the library are available in the following guides:

* [Getting started](https://developers.mindee.com/docs/php-getting-started)
* [PHP Command Line Interface (CLI)](https://developers.mindee.com/docs/php-cli)
* [PHP Generated APIs](https://developers.mindee.com/docs/php-generated-api)
* [PHP Custom APIs (API Builder - Deprecated)](https://developers.mindee.com/docs/php-api-builder)
* [PHP Invoice OCR](https://developers.mindee.com/docs/php-invoice-ocr)
* [PHP Receipt OCR](https://developers.mindee.com/docs/php-receipt-ocr)
* [PHP Financial Document OCR](https://developers.mindee.com/docs/php-financial-document-ocr)
* [PHP Passport OCR](https://developers.mindee.com/docs/php-passport-ocr)
* [PHP Resume OCR](https://developers.mindee.com/docs/php-resume-ocr)
* [PHP International Id OCR](https://developers.mindee.com/docs/php-international-id-ocr)
* [PHP FR Bank Account Detail OCR](https://developers.mindee.com/docs/php-fr-bank-account-details-ocr)
* [PHP FR Carte Grise OCR](https://developers.mindee.com/docs/php-fr-carte-grise-ocr)
* [PHP FR Health Card OCR](https://developers.mindee.com/docs/php-fr-health-card-ocr)
* [PHP FR ID Card OCR](https://developers.mindee.com/docs/php-fr-carte-nationale-didentite-ocr)
* [PHP US Bank Check OCR](https://developers.mindee.com/docs/php-us-bank-check-ocr)
* [PHP Barcode Reader API](https://developers.mindee.com/docs/php-barcode-reader-ocr)
* [PHP Cropper API](https://developers.mindee.com/docs/php-cropper-ocr)
* [PHP Invoice Splitter API](https://developers.mindee.com/docs/php-invoice-splitter-ocr)
* [PHP Multi Receipts Detector API](https://developers.mindee.com/docs/php-multi-receipts-detector-ocr)

You can view the source code on [GitHub](https://github.com/mindee/mindee-api-php).

You can also take a look at the
**[Reference Documentation](https://mindee.github.io/mindee-api-php)**.

## License
Copyright © Mindee

Available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).

## Questions?
[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
