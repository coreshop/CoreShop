<?php

require __DIR__ . '/behat-bootstrap.php';
require __DIR__ . '/app/TestAppKernel.php';

$kernel = new TestAppKernel('test', true);
Pimcore::setKernel($kernel);
$kernel->boot();