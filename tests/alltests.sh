#!/bin/bash

mkdir -p output

/Applications/MAMP/bin/php/php5.6.10/bin/php phpunit.php --verbose --bootstrap bootstrap.php --log-json output/log.xml AllTests

