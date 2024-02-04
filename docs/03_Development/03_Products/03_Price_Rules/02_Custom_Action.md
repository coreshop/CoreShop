---
title: Custom Action
---

# Creating Custom Product Price Rule Action

CoreShop offers a variety of default actions for product price rules. However, there may be instances where you need to implement custom actions. This guide will walk you through the process of creating a custom action for product price rules in CoreShop.

## Step 1: Create the Action Class

Depending on the type of discount, you can choose between different options (see [Pricing](../02_Price_Calculation.md) for more details)

 - **Price Action**: To define a new fixed price (`CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface`)
 - **Price Discount Action**: To define a new discounted price (`CoreShop\Component\Product\Rule\Action\ProductDiscountPriceActionProcessorInterface`)
 - **Discount Action**: To define a Discount on a given Price (`CoreShop\Component\Product\Rule\Action\ProductDiscountActionProcessorInterface`)

Each one is responsible for a different type of discount. The following example shows how to create a new fixed price discount.

```php
<?php
// src/CoreShop/Product/PriceRules/Action/CustomAction.php
declare(strict_types=1);

namespace App\CoreShop\Product\PriceRules\Action;

use CoreShop\Bundle\PurchaseOrderBundle\Calculator\WholesalePriceCalculatorInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Product\Exception\NoRetailPriceFoundException;
use CoreShop\Component\Product\Rule\Action\ProductPriceActionProcessorInterface;

class CustomAction implements ProductPriceActionProcessorInterface
{
    public function __construct(
        protected WholesalePriceCalculatorInterface $wholesalePriceCalculator,
        protected CurrencyConverterInterface $moneyConverter,
        protected CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function getPrice($subject, array $context, array $configuration): int
    {
        if (!$subject instanceof ProductInterface) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        //Return the Price of the product (remember that CoreShop uses integers for prices, 100 is 1.00)
        //You are also responsible to convert the price to the correct currency
        //the $context array contains the current context for calculation, e.g. the current currency
        return 100;
    }
}
```

Register the action in your services configuration:

```yaml
# config/services.yaml
services:
  App\CoreShop\Product\PriceRules\Action\CustomAction:
    tags:
      - { name: coreshop.product_price_rule.action, type: custom  }
```

## Resolving Autowiring Issues with Custom Actions

When autowiring is enabled in your CoreShop project, you may encounter a scenario where your custom action appears twice in the system: once with configuration options and once without. This duplication can be resolved by modifying your service definition to explicitly disable autoconfiguration for your custom action. Here’s how you can achieve this:

In your `services.yaml`, update the service definition for your custom action as follows:

```yaml
# config/services.yaml
services:
  App\CoreShop\Product\PriceRules\Action\CustomAction:
      autoconfigure: false
      tags:
        - { name: coreshop.product_price_rule.action, type: custom  }
```

This modification tells Symfony not to autoconfigure the custom action service, thus preventing the duplication issue. With this change, your custom action will appear only once in the CoreShop system with the intended configuration.
