# CoreShop Product Units

Units for Products can be defined globally in the Pimcore Backend on CoreShopMenu > Product > Product Units.

Each Product can have different units. Also each product/unit relation can have an own precision that differs to other relations. To archive that, the relation between products and units consists of three objects:
- ProductUnit
- ProductUnitDefinition
- ProductUnitDefinitions

As already said, the ProductUnit can be created in Pimcore Backend and consists mainly of the key for unit like PCS, CNT etc. and some localized fields for rendering in frontend.

To connect a ProductUnit to a CoreShopProduct in Backend you need to get the ProductUnit via repository and create a ProductUnitDefinition.

```php
use CoreShop\Component\Product\Model\Product;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinition;

$product = Product::getById(1);

/** @var ProductUnitInterface $unit */
$unit = $this->unitRepository->findOneBy(['name' => '']);

$unitDefinition = new ProductUnitDefinition();
$unitDefinition->setConversionRate(1.0); // optional
$unitDefinition->setPrecision(0);        // optional
$unitDefinition->setUnit($unit);

$product->getUnitDefinitions()->setDefaultUnitDefinition($unitDefinition);
$product->save();
```

To add multiple units for a product use `$product->addUnitDefinition($unitDefinition)`.

To change a unit override the default one `$product->getUnitDefinitions()->getDefaultUnitDefinition()->setUnit($otherUnit)`.
