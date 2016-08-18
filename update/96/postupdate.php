<?php

use Pimcore\Model\Object\ClassDefinition;

$list = new ClassDefinition\Listing();
$list->load();

foreach ($list->getClasses() as $class) {
    $class->save();
}