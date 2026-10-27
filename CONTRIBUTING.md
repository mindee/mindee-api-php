# Contributing to mindee-api-php

:+1::tada: First off, thanks for taking the time to contribute! :tada::+1:

The following is a set of guidelines for contributing to mindee-api-php, which is hosted on GitHub.
These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

## Code of Conduct

This project and everyone participating in it is governed by the following [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to [contact@mindee.com](mailto:contact@mindee.com).

## How Can I Contribute

### Reporting Bugs

Bugs are tracked as [GitHub issues](https://docs.github.com/en/issues).

To help maintainers and the community to be efficient, follow these guidelines:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible. When listing steps, **don't just say what you did, but explain how you did it**: which client and method you called, with which options, and what kind of file you sent (PDF, image, number of pages...).
* **Provide specific examples to demonstrate the steps**. Include links to files or GitHub projects, or copy/pasteable snippets, which you use in those examples. If you're providing snippets in the issue, use [Markdown code blocks](https://docs.github.com/en/get-started/writing-on-github/working-with-advanced-formatting/creating-and-highlighting-code-blocks).
* **Describe the behavior you observed after following the steps** and point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why.**
* **If the problem wasn't triggered by a specific action**, describe what you were doing before the problem happened and share more information using the guidelines below.
* **Can you reliably reproduce the issue?** If not, provide details about how often the problem happens and under which conditions it normally happens.

> **Note:** If you find a **Closed** issue that seems like it is the same thing that you're experiencing, open a new issue and include a link to the original issue in the body of your new one.

Include details about your configuration and environment:

* **Which version of mindee-api-php are you using?** (`composer show mindee/mindee`)
* **Which PHP version, on which OS and architecture?** (`php -v`, which also says whether PHP is thread-safe, ZTS, or not, NTS)
* **For PDF and image features: which version of the bernard-ledit extension?** (`php -r 'echo phpversion("bernard_ledit");'`)

If the problem is in the bernard-ledit extension itself, for example a crash, or a PDF or image it renders wrongly whatever the SDK does, report it on [mindee/bernard-ledit](https://github.com/mindee/bernard-ledit/issues) instead.

### Suggesting Enhancements

Enhancement suggestions are tracked as [GitHub issues](https://docs.github.com/en/issues).

Make sure to provide the following information:

* **Use a clear and descriptive title** for the issue to identify the suggestion.
* **Provide a step-by-step description of the suggested enhancement** in as many details as possible.
* **Provide specific examples to demonstrate the steps**. Include copy/pasteable snippets which you use in those examples, as [Markdown code blocks](https://docs.github.com/en/get-started/writing-on-github/working-with-advanced-formatting/creating-and-highlighting-code-blocks).
* **Describe the current behavior** and **explain which behavior you expected to see instead** and why.
* **Specify which version of mindee-api-php you're using**, and your PHP version and OS.

### Pull Requests

The process described here has several goals:

- Maintain mindee SDK quality
- Fix problems that are important to users
- Engage the community in working toward the best possible SDK
- Enable a sustainable system for mindee's maintainers to review contributions

Please follow these steps to have your contribution considered by the maintainers:

1. Follow all instructions in [the template](.github/PULL_REQUEST_TEMPLATE.md)
2. Follow the [styleguides](#styleguides)
3. Make sure the linters and the unit tests pass (see [Development](#development))

While the prerequisites above must be satisfied prior to having your pull request reviewed, the reviewer(s) may ask you to complete additional design work, tests, or other changes before your pull request can be ultimately accepted.

## Development

### Setup

You need PHP 8.1 or later, with the `curl`, `fileinfo` and `json` extensions, and [Composer](https://getcomposer.org/).

The test files live in a Git submodule, so clone with it:

```bash
git clone --recurse-submodules https://github.com/mindee/mindee-api-php.git
cd mindee-api-php
composer install
```

In an existing clone, run `git submodule update --init --recursive` instead.

`composer install` also sets up Git hooks through [Whisky](https://github.com/ProjektGopher/whisky): `composer lint` runs before each commit, and `composer test:unit` before each push.

### PDF and image features: the bernard-ledit extension

PDF and image operations (page cutting and merging, compression, crop and split extraction) rely on [bernard-ledit](https://github.com/mindee/bernard-ledit), a native PHP extension. It is optional for users, but you need it to run the full unit test suite.

Install it with [PIE](https://github.com/php/pie):

```bash
composer setup:bernard-ledit                  # latest stable release
composer setup:bernard-ledit -- 1.0.0         # a given version
```

This runs `tools/install-bernard-ledit.sh`, which installs the extension for the `php` on your PATH (set `PHP` to target another one), enables it, and checks that it works. On Windows, run `pie install mindee/bernard-ledit` directly. PIE may ask for your password to write to PHP's extension directory.

Check that it loaded:

```bash
php -r 'echo phpversion("bernard_ledit"), PHP_EOL;'
```

PHPStan reads the extension's classes from `stubs/bernard_ledit.stubs.php`. When you change the bernard-ledit version the SDK supports, also update the stubs, and the `conflict` range in `composer.json`:

```bash
composer update:bernard-ledit-stubs           # stubs of the latest stable release
composer update:bernard-ledit-stubs v1.0.0    # stubs of a given release
```

### Running the tests

| Command                        | What it runs                                                                    |
|--------------------------------|---------------------------------------------------------------------------------|
| `composer test:unit`           | Unit tests (`*Test.php`); needs bernard-ledit                                   |
| `composer test:pdf-features`   | Tests of the SDK's behavior when bernard-ledit is missing (`*TestPdf.php`); run them without the extension |
| `composer test:functional`     | Tests against the Mindee API (`*Functional.php`); needs API keys, see below     |
| `composer test:smoke:v1`, `composer test:smoke:v2` | The code samples                                            |
| `composer test:cli:v1`, `composer test:cli:v2`     | The command-line interface                                   |

Functional tests read their API keys and model IDs from environment variables, mainly `MINDEE_API_KEY`, `MINDEE_V2_API_KEY` and the `MINDEE_V2_*_MODEL_ID` variables. See `.github/workflows/_test-integrations.yml` for the full list.

### Linting and formatting

```bash
composer lint      # PHPStan, php-cs-fixer and Rector, without changing any file
composer format    # applies php-cs-fixer's and Rector's fixes
```

## Styleguides

### Git Commit Messages

* Start the message with the [gitmoji](https://gitmoji.dev/) that fits the change, followed by a short description in lowercase
* Use the present tense ("add feature" not "added feature")
* Use the imperative mood ("move cursor to..." not "moves cursor to...")
* Limit the first line to 72 characters or fewer
* Reference issues and pull requests liberally after the first line

Examples:

* :memo: add usage section in README
* :sparkles: add support for split extraction
* :bug: fix page index out of range on multi-page crops

### PHP Styleguide

We keep our code base consistent: PHP code follows the rules in [`.php-cs-fixer.dist.php`](.php-cs-fixer.dist.php), which build on [PER Coding Style](https://www.php-fig.org/per/coding-style/), the successor of PSR-12. PHPStan ([`phpstan.neon`](phpstan.neon)) and Rector ([`rector.php`](rector.php)) also run on every commit. `composer format` fixes most style issues for you.
