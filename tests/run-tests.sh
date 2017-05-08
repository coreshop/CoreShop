#!/bin/bash

cd src/CoreShop

set -e

# switch interpreter
if [[ "$TRAVIS_PHP_VERSION" == *"hhvm"* ]]; then CMD="hhvm"; else CMD="php"; fi

CMD="$CMD -c config/phpunit.xml --bootstrap bootstrap.php  --log-json output/log.xml AllTests"

echo $CMD
eval $CMD
