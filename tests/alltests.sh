#!/bin/bash

mkdir -p output

../../../vendor/bin/phpunit --verbose --bootstrap bootstrap.php --log-json output/log.xml AllTests

