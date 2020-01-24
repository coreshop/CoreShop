# CoreShop Product Unit Definitions

CoreShop uses Pimcore Data Objects to persist Product Information. But, it adds a little wrapper around it to be more
dynamic and configurable. It uses a Factory and Repository Pattern to do that.

## Create

If you want to create a new Product, we need to get our Factory Service for that:

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(761);

$unitRepository = $this->container->get('coreshop.repository.product_unit');

/** @var ProductUnitDefinitionInterface $defaultUnitDefinition */
$defaultUnitDefinition = $this->container->get('coreshop.factory.product_unit_definition')->createNew();
$defaultUnitDefinition->setUnit($unitRepository->findByName('Kubikmeter'));

/** @var ProductUnitDefinitionsInterface $unitDefinitions */
$unitDefinitions = $this->container->get('coreshop.factory.product_unit_definitions')->createNew();

$unitDefinitions->setDefaultUnitDefinition($defaultUnitDefinition);
$unitDefinitions->setProduct($product);

$product->setUnitDefinitions($unitDefinitions);

$product->save();
```

No we have our product and we can set all needed values.

If you now want to save it, just call the save function

```php
$product->save();
```


## Update

Update works the same as you are used to in Pimcore

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(761);

$unitRepository = $this->container->get('coreshop.repository.product_unit');

$defaultUnitDefinition = $product->getUnitDefinitions()->getDefaultUnitDefinition();
$defaultUnitDefinition->setUnit($unitRepository->findByName('G'));

$unitDefinitionsRepository = $this->container->get('coreshop.repository.product_unit_definitions');

/** @var ProductUnitDefinitions $unitDefinitions */
$unitDefinitions = $unitDefinitionsRepository->findOneForProduct($product);
$unitDefinitions->setDefaultUnitDefinition($defaultUnitDefinition);

$product->setUnitDefinitions($unitDefinitions);

$product->save();
```

## Delete

Delete works the same as you are used to in Pimcore

```php
/** @var DataObject\CoreShopProduct $product */
$product = DataObject::getById(1);

$unitDefinitionsRepository = $this->container->get('coreshop.repository.product_unit_definitions');
$item = $unitDefinitionsRepository->findOneForProduct($product);

$unitDefinitionsRepository->remove($item);
```
