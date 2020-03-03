# CoreShop Product Unit Definitions

CoreShop has a great new feature for product units. E.g. you can sell items "per meter" etc.

First, add some product units in Pimcore > CoreShop > Product > Product Units. Click "Add" and fill all the fields.

Then you can add product-units directly in the product-objects inside Pimcore (take a look at the "Price"-tab). There, you can also add multiple product units (eg: 1 box contains of 12 items - CoreShop will show you different order-possibilities in the add-to-cart section in the webshop)

Using the API for product units:

## Create Default UnitDefinition

If you want to create a new Product, we need to get our Factory Service for that:

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(1);

$unitRepository = $container->get('coreshop.repository.product_unit');

/** @var ProductUnitDefinitionInterface $defaultUnitDefinition */
$defaultUnitDefinition = $container->get('coreshop.factory.product_unit_definition')->createNew();
$defaultUnitDefinition->setUnit($unitRepository->findByName('Kubikmeter'));

/** @var ProductUnitDefinitionsInterface $unitDefinitions */
$unitDefinitions = $container->get('coreshop.factory.product_unit_definitions')->createNew();

$unitDefinitions->setDefaultUnitDefinition($defaultUnitDefinition);
$unitDefinitions->setProduct($product);

$product->setUnitDefinitions($unitDefinitions);

$product->save();
```



## Update Default UnitDefinition

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(1);

$unitRepository = $container->get('coreshop.repository.product_unit');

$defaultUnitDefinition = $product->getUnitDefinitions()->getDefaultUnitDefinition();
$defaultUnitDefinition->setUnit($unitRepository->findByName('Liter'));

$unitDefinitionsRepository = $container->get('coreshop.repository.product_unit_definitions');

/** @var ProductUnitDefinitions $unitDefinitions */
$unitDefinitions = $unitDefinitionsRepository->findOneForProduct($product);
$unitDefinitions->setDefaultUnitDefinition($defaultUnitDefinition);

$product->setUnitDefinitions($unitDefinitions);

$product->save();
```



## Delete UnitDefinition

Deleting a UnitDefiniton from a product is done by finding the UnitDefinitions for the product in the product_unit_definitions repository and then deleting it.

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(1);

$unitDefinitionsRepository = $container->get('coreshop.repository.product_unit_definitions');
$item = $unitDefinitionsRepository->findOneForProduct($product);

$unitDefinitionsRepository->remove($item);
```
