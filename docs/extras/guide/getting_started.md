This guide will help you get started with the Mindee PHP OCR SDK to easily extract data from your documents.

The PHP OCR SDK supports [invoice](https://developers.mindee.com/docs/php-invoice-ocr), [passport](https://developers.mindee.com/docs/php-passport-ocr), [receipt](https://developers.mindee.com/docs/php-receipt-ocr) OCR APIs and [custom-built API](https://developers.mindee.com/docs/php-api-builder) from the API Builder.

You can view the source code on [GitHub](https://github.com/mindee/mindee-api-php), and the package on [PyPI](https://pypi.org/project/mindee/).

## Prerequisite

- Download and install [PHP](https://www.php.net/downloads.php). This library is officially supported on PHP `7.4` to `8.2`.
- Download and install [Composer](https://getcomposer.org/download/).

## Installation

To quickly get started with the PHP OCR SDK anywhere, the preferred installation method is via `composer`.

```shell
composer require mindee/mindee
```

### Development Installation

If you'll be modifying the source code, you'll need to install the development requirements to get started.

1. First clone the repo.

```shell
git clone git@github.com:mindee/mindee-api-php.git
```

2. Then navigate to the cloned directory and install all development requirements.

```shell
cd mindee-api-php
composer install
```

## Updating the Version

It is important to always check the version of the Mindee OCR SDK you are using, as new and updated features won’t work on old versions.

To check the installed version:

```shell
composer show mindee/mindee
```

To get the latest version:

```shell
composer require mindee/mindee
```

To install a specific version:

```shell
composer require mindee/mindee:<your_version>
```

## Usage

To get started with Mindee's APIs, you need to create a `Client` and you're ready to go.

Let's take a deep dive into how this works.

## Initializing the Client

The `Client` centralizes document configurations in a single object.

The `Client` requires your [API key](https://developers.mindee.com/docs/make-your-first-request#create-an-api-key).

You can either pass these directly to the constructor or through environment variables.

### Pass the API key directly

```php
<?php
use Mindee\Client;

// Init a new client
$mindeeClient = new Client("my-api-key");
```

### Set the API key in the environment

API keys should be set as environment variables, especially for any production deployment.

The following environment variable will set the global API key:

```shell
MINDEE_API_KEY="my-api-key"
```

Then in your code:

```php
use Mindee\Client;

// Init a new client
$mindeeClient = new Client();
```

### Setting the Request Timeout

The request timeout can be set using an environment variable:

```shell
MINDEE_REQUEST_TIMEOUT=200
```

## Loading a Document File

Before being able to send a document to the API, it must first be loaded.

You don't need to worry about different MIME types, the library will take care of handling  
all supported types automatically.

Once a document is loaded, interacting with it is done in exactly the same way, regardless  
of how it was loaded.

There are a few different ways of loading a document file, depending on your use case:

- [Path](#path)
- [File Object](#file-object)
- [Base64](#base64)
- [Bytes](#bytes)
- [URL](#url)

### Path

Load from a file directly from disk. Requires an absolute path, as a string.

```php
$inputSource = $mindeeClient->sourceFromPath("/path/to/the/file.ext");
```

### File Object

A normal PHP file object. **Must be in binary mode**.

```php
$myFile = file("/path/to/the/file.ext");
$inputSource = $mindeeClient->sourceFromFile($myFile);
```

### Base64

Requires a base64 encoded string.

**Note**: The original filename is required when calling the method.

```php
$b64String = "/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLD....";
$inputSource = $mindeeClient->sourceFromB64($b64String, "receipt.jpg");
```

### Bytes

Requires raw bytes.

**Note**: The original filename is required when calling the method.

```php
$rawBytes = b"%PDF-1.3\n%\xbf\xf7\xa2\xfe\n1 0 ob...";
$inputSource = $mindeeClient->sourceFromBytes($rawBytes, "receipt.jpg");
```

### URL

Allows sending a URL directly.

**Note**: No local operations can be performed on the input (such as removing pages from a PDF).

```php
$inputSource = $mindeeClient->sourceFromUrl("https://www.example.com/invoice.pdf");
```

## Sending a File

To send a file to the API, we need to specify how to process the document.  
This will determine which API endpoint is used and how the API return will be handled internally by the library.

More specifically, we need to set a `Mindee\Product` class as the first parameter of the `parse` method.

This is because the `parse` method's return type depends on its first argument.

Product classes inherit from the base `Mindee\Parsing\Common\Inference` class.

More information is available in each document-specific guide.

### Off-the-Shelf Documents

Simply setting the correct class and passing the input document is enough:

```php
$result = $mindeeClient->parse(InvoiceV4::class, $inputSource);
```

### Custom Documents

The endpoint to use must be created beforehands and subsequently passed to the `endpoint` argument of the `parse` method:

```php
// Create a custom endpoint
$customEndpoint = $mindeeClient->createEndpoint(
    "my-endpoint",
    "my-account",
//  "my-version" // Optional
);

// Add the custom endpoint to the prediction options.
$predictOptions = new PredictMethodOptions();
$predictOptions->setEndpoint($customEndpoint);

// Parse the file
$apiResponse = $mindeeClient->parse(CustomV1::class, $inputSource, $predictOptions);
```

This is because the `CustomV1` class is enough to handle the return processing, but the actual endpoint needs to be specified.


## Processing the Response

Results of a prediction can be retrieved in two different places:

- [Document level predictions](#document-level-prediction)
- [Page level predictions](#page-level-prediction)

### Document Level Prediction

The `document` attribute is an object specific to the type of document being processed.  
It is an instance of the `Document` class, to which a generic type is given.

It contains the data extracted from the entire document, all pages combined.  
It's possible to have the same field in various pages, but at the document level only the highest confidence field data will be shown (this is all done automatically at the API level).

Usage:
```php
echo $result->document;
```

A `document`'s fields (attributes) can be accessed through it's `prediction` attribute, which have types that can vary from one product to another.  
These attributes are detailed in each product's respective guide.

### Page Level Prediction

The `pages` attribute is a list of `Page` objects. `Page` is a wrapper around elements that extend the [`Document` class](#document-level-prediction).  
The `prediction` of a `Page` inherits from the product's own `Document`, and adds all page-specific fields to it.

The order of the elements in the list matches the order of the pages in the document.

All response objects have a `pages` property, regardless of the number of pages.  
Single-page documents will have a single entry.

Iteration over `pages` is done like with any list, for example:

```php
foreach ($response->pages as $page){
    echo $page;
}
```

## Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
