## Command Line Usage

The CLI tool is provided mainly for quick tests and debugging.

The CLI ships **two top-level groups**:

* V2 product subcommands at the root: `extraction`, `classification`,
  `crop`, `ocr`, `split`, plus `search-models`.
* All V1 products live under a `v1` group (e.g. `mindee v1 invoice ...`).

For backward compatibility, invoking a V1 product name directly
(`mindee invoice ...`, `mindee receipt ...`, …) is automatically
dispatched to `mindee v1 <product> ...`.

### General help

```shell
./mindee --help
./mindee v1 --help
./mindee extraction --help
./mindee search-models --help
```

> Note: Due to the limited-nature of most PHP CLI tools, the help sections aren't customized for each command.

## V1 commands

### Example parse command for Off-the-Shelf document

```shell
./mindee v1 invoice -k xxxxxxx /path/to/invoice.pdf
```

> Legacy `./mindee invoice -k xxxxxxx /path/to/invoice.pdf` still works.

### Works with environment variables

```shell
export MINDEE_API_KEY=xxxxxx
./mindee v1 invoice /path/to/invoice.pdf
```

### Example parse command for a generated document (docTI)

```shell
./mindee v1 generated -a pikachu -k xxxxxxx pokemon_card /path/to/card.jpg -A
```


### Example async parse command

```shell
./mindee v1 invoice-splitter path/to/the/invoice.pdf -A
```

> Note: the `-A` can be omitted on products which do not support synchronous mode.

### Full parsed output

```shell
./mindee v1 invoice -k xxxxxxx /path/to/invoice.pdf -o raw
```

## V2 commands

V2 inference commands share the same option set:

| Option | Short | Description |
|--------|-------|-------------|
| `--model-id` | `-m` | ID of the model to use (required). |
| `--api-key` | `-k` | API key. Falls back to `MINDEE_V2_API_KEY`. |
| `--alias` | `-a` | Optional alias for the file. |
| `--output` | `-o` | `summary` (default), `full`, or `raw`. |

The `extraction` command adds:

| Option | Short | Description |
|--------|-------|-------------|
| `--rag` | `-g` | Enable Retrieval-Augmented Generation. |
| `--raw-text` | `-r` | Extract the document's raw text. |
| `--confidence` | `-c` | Return per-field confidence scores. |
| `--polygon` | `-p` | Return per-field bounding polygons. |
| `--text-context` | `-t` | Add text context to the API call. |

### Example V2 extraction call

```shell
export MINDEE_V2_API_KEY=xxxxxx
./mindee extraction -m <model-id> /path/to/file.pdf
```

### Example V2 extraction with options and a JSON dump

```shell
./mindee extraction -m <model-id> -k <api-key> -r -c -p -o full /path/to/file.pdf
./mindee extraction -m <model-id> -o raw /path/to/file.pdf
```

### Listing models

```shell
./mindee search-models -k <api-key>
./mindee search-models --name fin --model-type extraction -r
```

### Running the script through php

A helper script allows you to start the command directly:

```shell
php bin/cli.php
```

## Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
