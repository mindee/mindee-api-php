#!/bin/sh
set -e

TEST_FILE=$1

if [ -z "$TEST_FILE" ]; then
  TEST_FILE='./tests/resources/file_types/pdf/blank_1.pdf'
fi
echo "TEST_FILE: ${TEST_FILE}"

CLI_PATH="./bin/cli.php"
echo "CLI_PATH: ${CLI_PATH}"

PRODUCTS="financial-document receipt invoice invoice-splitter"
PRODUCTS_SIZE=4
i=1

for product in $PRODUCTS
do
  echo "--- Test $product with Summary Output ($i/$PRODUCTS_SIZE) ---"
  SUMMARY_OUTPUT=$(php "$CLI_PATH" v1 "$product" "$TEST_FILE")
  if [ -z "$SUMMARY_OUTPUT" ]; then
    echo "Error: no $product output"
    exit 1
  fi
  echo "$SUMMARY_OUTPUT"
  echo ""
  echo ""
  sleep 0.5
  i=$((i + 1))
done
