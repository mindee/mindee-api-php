#!/bin/sh
set -e

TEST_FILE=$1

if [ -z "$TEST_FILE" ]; then
  TEST_FILE='./tests/resources/file_types/pdf/blank_1.pdf'
fi
echo "TEST_FILE: ${TEST_FILE}"

CLI_PATH="./bin/cli.php"
echo "CLI_PATH: ${CLI_PATH}"

echo "--- Test model list retrieval"
MODELS=$(php "$CLI_PATH" search-models)
if [ -z "$MODELS" ]; then
  echo "Error: no models found"
  exit 1
else
  echo "Models retrieval OK"
fi

run_test() {
  model_id="$1"
  model_type="$2"

  echo "--- Test $model_type ID: $model_id"
  SUMMARY_OUTPUT=$(php "$CLI_PATH" "$model_type" -m "$model_id" "$TEST_FILE")
  if [ -z "$SUMMARY_OUTPUT" ]; then
    echo "Error: no $model_type output"
    exit 1
  fi
  echo "$SUMMARY_OUTPUT"
  echo ""
  echo ""
  sleep 0.5
}

run_test "$MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID" "extraction"
run_test "$MINDEE_V2_SE_TESTS_CROP_MODEL_ID" "crop"
run_test "$MINDEE_V2_SE_TESTS_SPLIT_MODEL_ID" "split"
run_test "$MINDEE_V2_SE_TESTS_CLASSIFICATION_MODEL_ID" "classification"
run_test "$MINDEE_V2_SE_TESTS_OCR_MODEL_ID" "ocr"
