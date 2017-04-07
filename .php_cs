<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['views'])
    ->in([__DIR__]);

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        '@PSR1' => true,
        '@Symfony' => true,

        'array_syntax' => array('syntax' => 'short'),
    ))
    ->setFinder($finder)
;