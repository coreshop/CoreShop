<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(['views'])
    ->in([__DIR__]);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(array('encoding', 'short_tag'))
    ->finder($finder)
    ;
