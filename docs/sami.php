<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('vendor')
    ->exclude('tests')
    ->in('../')
;

$xml = simplexml_load_string(file_get_contents("../plugin.xml"));

return new Sami($iterator, array(
    'build_dir' => __DIR__ . '/build',
    'cache_dir' => __DIR__ . '/cache',
    'title' => 'CoreShop API (Build: ' . $xml->plugin->pluginRevision . ')',
));
