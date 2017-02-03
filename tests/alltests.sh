#!/bin/bash

mkdir -p output

../../../vendor/bin/phpunit --verbose --bootstrap bootstrap.php AllTests

