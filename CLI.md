## Command Line Usage

The CLI tool is provided mainly for quick tests and debugging.

### General help

```shell
./mindee generated --help
```

> Note: Due to the limited-nature of most PHP CLI tools, the help sections aren't customized for each command.

### Example parse command for Off-the-Shelf document

```shell
./mindee invoice -k xxxxxxx /path/to/invoice.pdf
```

### Works with environment variables

```shell
export MINDEE_API_KEY=xxxxxx
./mindee invoice /path/to/invoice.pdf
```

### Example parse command for a generated document (DocTI)

```shell
./mindee generated -a pikachu -k xxxxxxx pokemon_card /path/to/card.jpg -A
```


### Example async parse command

```shell
./mindee invoice-splitter path/to/the/invoice.pdf -A
```

> Note: the `-A` can be omitted on products which do not support synchronous mode.

```shell
./mindee invoice-splitter path/to/the/invoice.pdf -A
```

### [DEPRECATED] Example parse command for a custom document (API Builder)

```shell
./mindee custom -a pikachu -k xxxxxxx pokemon_card /path/to/card.jpg
```

### Full parsed output

```shell
./mindee invoice -k xxxxxxx /path/to/invoice.pdf -o raw
```

### Running the script through php

A helper script allows you to start the command directly:

```shell
php bin/cli.php
```

## Questions?

[Join our Slack](https://join.slack.com/t/mindee-community/shared_invite/zt-2d0ds7dtz-DPAF81ZqTy20chsYpQBW5g)
