# Product Price Calculation

CoreShop employs multiple Price Calculators to determine the correct price for a product. By default, the following
calculators are used:

- [Price Rule Calculator](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/ProductBundle/Calculator/PriceRuleCalculator.php):
  Utilizes prices from Catalog Price Rules and Specific Price Rules.
- [Store Product Price Calculator](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Product/Calculator/StoreProductPriceCalculator.php):
  Calculates prices based on store values.

For custom calculators, implement the
interface [```CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface```](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Product/Calculator/ProductPriceCalculatorInterface.php)
and register your service with the tag ```coreshop.product.price_calculator```, including attributes ```type```
and ```priority```.

## CoreShop Price Calculation Components

CoreShop's price calculation encompasses three distinct prices:

- **Retail Price**: The base price without any discounts.
- **Discount Price**: A special price for promotions, which should be lower than the retail price.
- **Discount**: Monetary value of discounts from promotions.
- **Price**: Calculated as the Retail Price or Discount Price (whichever is applicable) minus any discount rules.

## Calculator Service Usage

To calculate the price for a product, use one of the following services:

1. ```coreshop.product.price_calculator```: Calculates prices without tax considerations.
2. ```coreshop.product.taxed_price_calculator```: Calculates prices with or without tax considerations (recommended).

### Templating

For price calculation within a template, use the ```coreshop_product_price``` filter:

```twig
{{ (product|coreshop_product_price(true)) }}
```

Custom Price Calculator Example
This example demonstrates how to add a new calculator, using the property "price" - 1 as the Product Price, and -1 as
the Discount. Note that this example is a demonstration and not a practical implementation.

```php

<?php

namespace App\CoreShop\Product;

use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class CustomPriceCalculator implements ProductPriceCalculatorInterface
{
    // Implementation of methods
}
```

Registration of the custom service in the container:

```yml
App\CoreShop\Product\CustomPriceCalculator:
  tags:
    - { name: coreshop.product.price_calculator, type: custom, priority: 1 }
```

With this setup, CoreShop will now use your custom service for all Product Price Calculations.
