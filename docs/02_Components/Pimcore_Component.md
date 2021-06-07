# CoreShop Pimcore Component

## Features

### Data Object Features

#### Class Converter and Data Migrate
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

#### Class Installer
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

#### Class/Brick/Field Collection Updater
Definition Updaters help you in migrating your Pimcore Class/Bricks or Field Collection Definitions to be properly
migrated from Release to Release.

To update a Pimcore class use it like this:

```php
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;

$classUpdater = new ClassUpdate('Product');

//Your JSON Definition from Pimcore
$payment = [
    'fieldtype' => 'coreShopSerializedData',
    'phpdocType' => 'array',
    'allowedTypes' =>
        [
        ],
    'maxItems' => 1,
    'name' => 'paymentSettings',
    'title' => 'Payment Settings',
    'tooltip' => '',
    'mandatory' => false,
    'noteditable' => true,
    'index' => false,
    'locked' => null,
    'style' => '',
    'permissions' => null,
    'datatype' => 'data',
    'columnType' => null,
    'queryColumnType' => null,
    'relationType' => false,
    'invisible' => false,
    'visibleGridView' => false,
    'visibleSearch' => false,
];

//Check if field exists
if (!$classUpdater->hasField('paymentSettings')) {
    //If not insert field after a specific field and save the definition
    $classUpdater->insertFieldAfter('paymentProvider', $payment);
    $classUpdater->save();
}

```

Thats it, the same works for FieldCollections with the class `CoreShop\Component\Pimcore\DataObject\FieldCollectionDefinitionUpdate`
and for Bricks with the class `CoreShop\Component\Pimcore\DataObject\BrickDefinitionUpdate`

#### Inheritance Helper
Inhertiance Helper is a small little but very useful helper class to enable Pimcore inheritance only with a closure function like this:

```php

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;

$inheritedValue = InheritanceHelper::useInheritedValues(function() use($object) {
    return $object->getValueInherited();
}, true);

```

#### Version Helper
Version Helper is a small little but very useful helper class to disabling or enablind Pimcore Versioning.

```php

use CoreShop\Component\Pimcore\DataObject\VersionHelper;

VersionHelper::useVersioning(function() use($object) {
    //Object will be saved without creating a new Version
    $object->save();
}, false);

```

#### Unpublished Helper
Unpublsihed Helper is a small little but very useful helper class to get unpublished objects in Pimcore Frontend.

```php

use CoreShop\Component\Pimcore\DataObject\UnpublishedHelper;

$allProducts = UnpublishedHelper::hideUnpublished(function() use($object) {
    //Will return all products, even the unpbulished ones
    return $object->getProducts();
}, false);

```

### Expression Language Features
CoreShop adds some features to the Symfony Expression language like:

 - PimcoreLanguageProvider: to get Pimcore Objects, Assets or Documents inside a Expression Language Query

### Migration Features

#### Pimcore Shared Translations
Helps you to install new Shared Translations during Migration:

```php
use CoreShop\Component\Pimcore\Migration\SharedTranslation;

SharedTranslation::add('key', 'en', 'value');
```

### Routing Features

#### Link Generator
The CoreShop Link Generator is a wrapper arounds Symfony Routing Component, and helps you to easier generate routes for Pimcore Objects and Symfony Routes with one function.

```php
//Generate a route for a Pimcore Object with a Link Generator

$this->get('coreshop.link_generator')->generate($product, 'route_product', ['foo' => 'bar']);

//Generate a route for a Symfony Route or Pimcore Static Routes Route

$this->get('coreshop.link_generator')->generate(null, 'overview', ['foo' => 'bar']);
```

CoreShop also provides your with twig helpers for that:

```twig
{{ coreshop_path(product, 'coreshop_product_detail') }}

{{ coreshop_path('coreshop_cart_remove_price_rule', {'code' : priceRule.voucherCode }) }}

coreshop_path('coreshop_index')
```

### Twig Features
CoreShop adds a lot of additional twig functions to make a developers live even more enjoyable :)

#### Asset Helper Tests

These tests let you test if a certain object is a Pimcore Asset or one of its subtypes:

 - asset
 - asset_archive
 - asset_audio
 - asset_document
 - asset_folder
 - asset_image
 - asset_text
 - asset_unknown
 - asset_video

```twig
{% if image is asset_image %}
    {# Process Image #}
{% endif %}
```

#### Document Helper Tests
These tests let you test if a certain object is a Pimcore Document or one of its subtypes:

 - document
 - document_email
 - document_folder
 - document_hardlink
 - document_newsletter
 - document_page
 - document_link
 - document_page_snippet
 - document_print
 - document_print_container
 - document_print_page
 - document_snippet

```twig
{% if document is document_page %}
    {# Process Document #}
{% endif %}
```

#### Document Helper Class
These tests let you test if a certain object is a Pimcore DataObject or a DataObject of a specific class.

 - object
 - object_class($className)

```twig
{% if product is object %}
    {# product is of any DataObject Tyoe #}
{% endif %}

{% if product is object_class('Product') %}
    {# product is of a Product DataObject #}
{% endif %}
```
