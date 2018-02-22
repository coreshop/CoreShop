<?php

use Pimcore\Tests\Util\Autoloader;

if (!defined('PIMCORE_PROJECT_ROOT')) {
    define(
        'PIMCORE_PROJECT_ROOT',
        getenv('PIMCORE_PROJECT_ROOT')
            ?: getenv('REDIRECT_PIMCORE_PROJECT_ROOT')
            ?: realpath(getcwd())
    );
}

if (!defined('TESTS_PATH')) {
    define('TESTS_PATH', __DIR__);
}

define('PIMCORE_CLASS_DIRECTORY', __DIR__ . '/tmp/var/classes');

define('PIMCORE_TEST', true);

require_once PIMCORE_PROJECT_ROOT . '/pimcore/tests/_support/Util/Autoloader.php';
require_once PIMCORE_PROJECT_ROOT . '/pimcore/config/bootstrap.php';

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader->add('CoreShop\Test', [__DIR__.'/lib']);

Autoloader::addNamespace('Pimcore\Model\DataObject', __DIR__ . '/tmp/var/classes/DataObject');
