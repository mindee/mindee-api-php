#!/bin/sh
set -e

OUTPUT_FILE='../test_code_samples/_test.php'
ACCOUNT=$1
ENDPOINT=$2
API_KEY=$3

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
composer init --no-interaction --name "mindee/test_code_samples" --description "testing package" --author "mindee" --type "github"
composer install
composer config repositories.mindee/mindee '{"type": "path", "url":"../mindee-api-php"}'
composer require mindee/mindee:dev-main
composer dump-autoload
cd -

for f in $(find docs/code_samples -maxdepth 1 -name "*.txt" | sort -h)
do
  echo "###############################################"
  echo "${f}"
  echo "###############################################"
  echo

  sed "s/my-api-key/$API_KEY/" "${f}" > $OUTPUT_FILE
  sed -i "s/\/path\/to\/the\/file.ext/..\/mindee-api-php\/tests\/resources\/file_types\/pdf\/blank_1.pdf/" $OUTPUT_FILE

  if echo "$f" | grep -q "custom_v1.txt"
  then
    sed -i "s/my-account/$ACCOUNT/g" $OUTPUT_FILE
    sed -i "s/my-endpoint/$ENDPOINT/g" $OUTPUT_FILE
  fi

  if echo "${f}" | grep -q "default_sync.txt"
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

  sleep 0.6  # avoid too many request errors
  php $OUTPUT_FILE
done
