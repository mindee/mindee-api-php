#!/bin/sh
set -e

OUTPUT_FILE='../test_code_samples/_test.php'
ACCOUNT=$1
ENDPOINT=$2
API_KEY=$3
API_KEY_V2=$4
MODEL_ID=$5

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

for f in $(find docs/code_samples -maxdepth 1 -name "*.txt" -not -name "workflow_*.txt" | sort -h)
do
  if echo "${f}" | grep -q "default_v2.txt"; then
    if [ -z "${API_KEY_V2}" ] || [ -z "${MODEL_ID}" ]; then
      echo "Skipping ${f} (API_KEY_V2 or MODEL_ID not supplied)"
      echo
      continue
    fi
  fi
  echo
  echo "###############################################"
  echo "${f}"
  echo "###############################################"
  echo

  sed "s/my-api-key/$API_KEY/" "${f}" > $OUTPUT_FILE
  sed -i "s/\/path\/to\/the\/file.ext/..\/mindee-api-php\/tests\/resources\/file_types\/pdf\/blank_1.pdf/" $OUTPUT_FILE

  # Only keeping the sample for display in the UI
  if echo "$f" | grep -q "custom_v1.txt"
  then
    continue
  fi

  if echo "${f}" | grep -q "default.txt"
  then
    sed -i "s/my-account/$ACCOUNT/" $OUTPUT_FILE
    sed -i "s/my-endpoint/$ENDPOINT/" $OUTPUT_FILE
    sed -i "s/my-version/1/" $OUTPUT_FILE
  fi

  if echo "${f}" | grep -q "default_async.txt"
  then
    sed -i "s/my-account/mindee/" $OUTPUT_FILE
    sed -i "s/my-endpoint/invoice_splitter/" $OUTPUT_FILE
    sed -i "s/my-version/1/" $OUTPUT_FILE
  fi

  if echo "${f}" | grep -q "default_v2.txt"
  then
    sed -i "s/MY_API_KEY/$API_KEY_V2/" $OUTPUT_FILE
    sed -i "s/MY_MODEL_ID/$MODEL_ID/" $OUTPUT_FILE
  else
    sed -i "s/my-api-key/$API_KEY/" $OUTPUT_FILE
  fi

  sleep 0.6  # avoid too many request errors
  php -d auto_prepend_file=vendor/autoload.php $OUTPUT_FILE
done
