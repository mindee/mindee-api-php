# Mindee PHP API Library Changelog

## v1.2.0 - 2024-02-21
### Changes
* :sparkles: add support for International ID V2
* :sparkles: add support for Resume V1
* :sparkles: add support for EU Driver License

### Fixes
* :bug: fix display issue for rst columns that were exactly the length of a cell
* :recycle: properly parse bytes, base64 & file inputs (see #23 )
* :recycle: removed base64 coercion on most input types
* :bug: fix mimetype being improperly assigned for most input types
* :bug: fix an issue that prevented some errors from being displayed properly
* :memo: update documentation


## v1.1.0 - 2024-01-30
### Changes
* :arrow_up: update invoices to v4.4
* :sparkles: add support for `raw_value` in string fields


## v1.0.1 - 2024-01-23
### Fixes
* :bug: fix Fatal error including API version string in a namespaced project (Co-authored-by: superwave1999)


## v1.0.0 - 2024-01-08
* :tada: First official release!


## v1.0.0-RC2 - 2023-12-19
### Changes
* :memo: fix typos in README.md documentation
* :memo: fix typos in getting_started.md documentation
* :wrench: add code samples directly in documentation
* :recycle: uniformize name for default sample


## v1.0.0-RC1 - 2023-12-19
* :tada: First release!
