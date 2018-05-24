CoreShop Pimcore Component
==================

# CoreShop
This Component is part of the CoreShop Project (https://www.github.com/coreshop/CoreShop). But it's designed to be used
without as well.

# Integration into Pimcore without CoreShop

There is a separate Pimcore Bundle where part of this Component get glued to Symfony: (https://www.github.com/coreshop/PimcoreBundle)

# Features
## Class Converter and Data Migrate
Class converter is a small utility, which lets you migrate all Data from one class to another. Usage:

```php
<?php

use CoreShop\Component\Pimcore\Migrate;

$currentClassName = 'Product';
$newClassName = 'NewProduct';
$options = [
    'delete_existing_class' => true,
    'parentClass' => 'AppBundle\Model\MyProduct'
];

//Copies $currentClassName Definition to $newClassName
//$options can overwrite some properties like parentClass
Migrate::migrateClass($currentClassName, $newClassName, $options);

//This function migrates all data from $currentClassName to $newClassName
//It uses SQL Commands to increase performance of migration
Migrate::migrateData($currentClassName, $newClassName);
```

## Class Installer

Class Installer helps you importing Classes/FieldCollections/ObjectBricks into Pimcore based of a JSON Definition:

```php

use CoreShop\Component\Pimcore\ClassInstaller;

$installer = new ClassInstaller();

// For Bricks use
$installer->createBrick($pathToJson, $brickName);

// For Classes use
$installer->createClass($pathToJson, $className, $updateExistingClass);

// For FieldCollections use
$installer->createFieldCollection($pathToJson, $fcName);

```
