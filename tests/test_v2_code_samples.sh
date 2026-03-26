#!/bin/sh
set -e

OUTPUT_FILE='../test_code_samples/_test.php'

rm -fr ../test_code_samples
mkdir ../test_code_samples

cd ../test_code_samples

EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
rm composer-setup.php
composer init --no-interaction --name "mindee/test_code_samples" --description "testing package" --author "mindee" --type "project"
composer install
composer config repositories.mindee/mindee '{"type": "path", "url":"../mindee-api-php"}'
composer require mindee/mindee @dev
composer dump-autoload
chmod -R 777 ./
cd -

for f in $(find docs/code_samples -maxdepth 1 -name "v2_*.txt" | sort -h)
do
    echo "###############################################"
    echo "${f}"
    echo "###############################################"
    echo

    sed "s/MY_API_KEY/$MINDEE_V2_API_KEY/" "${f}" > $OUTPUT_FILE
    sed -i "s/\/path\/to\/the\/file.ext/tests\/resources\/file_types\/pdf\/blank_1.pdf/" $OUTPUT_FILE

    if echo "${f}" | grep -q "v2_classification"
    then
      sed -i "s/MY_MODEL_ID/${MINDEE_V2_SE_TESTS_CLASSIFICATION_MODEL_ID}/" $OUTPUT_FILE
    fi

    if echo "${f}" | grep -q "v2_crop"
    then
      sed -i "s/MY_MODEL_ID/${MINDEE_V2_SE_TESTS_CROP_MODEL_ID}/" $OUTPUT_FILE
    fi

    if echo "${f}" | grep -q "v2_extraction"
    then
      sed -i "s/MY_MODEL_ID/${MINDEE_V2_SE_TESTS_FINDOC_MODEL_ID}/" $OUTPUT_FILE
      sed -i "s/MY_WEBHOOK_ID/${MINDEE_V2_SE_TESTS_FAILURE_WEBHOOK_ID}/" $OUTPUT_FILE
    fi

    if echo "${f}" | grep -q "v2_ocr"
    then
      sed -i "s/MY_MODEL_ID/${MINDEE_V2_SE_TESTS_OCR_MODEL_ID}/" $OUTPUT_FILE
    fi

    if echo "${f}" | grep -q "v2_split"
    then
      sed -i "s/MY_MODEL_ID/${MINDEE_V2_SE_TESTS_SPLIT_MODEL_ID}/" $OUTPUT_FILE
    fi

    echo
  sleep 0.6  # avoid too many request errors
  php -d auto_prepend_file=vendor/autoload.php $OUTPUT_FILE
done
