<?php

require __DIR__ . '/behat-bootstrap.php';
require __DIR__ . '/app/TestAppKernel.php';

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader->add('CoreShop\Test', [__DIR__.'/lib']);

$kernel = new TestAppKernel('test', true);
$kernel->boot();