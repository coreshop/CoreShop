
#### Create Custom CoreShop Price Rule Conditions

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### After 1.2

```
website/lib/Website/CoreShop/Model/PriceRule/Condition/YourCondition.php

OR

YourPlugin/lib/YourPlugin/Model/PriceRule/Condition/YourCondition.php
```

```php
namespace Website\Model\PriceRule\Condition;

use CoreShop\Model\Cart;
use CoreShop\Model\PriceRule\Condition\AbstractCondition;
use CoreShop\Model\Product;

class YourCondition extends AbstractCondition
{
   public static $type = 'yourCondition';

   public function checkConditionCart(Cart $cart, PriceRule $priceRule, $throwException = false)
   {
       //Check if valid for cart
       return true;
   }

   public function checkConditionProduct(ProductModel $product, ProductModel\AbstractProductPriceRule $priceRule)
   {
       //Check if valid for product
       return true;
   }
}

```
If you need some configuration, you can create a JS file for the Shipping Rule UI.

```
YourPlugin/static/js/coreshop/pricerules/condition/yourCondition.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.yourCondition');

pimcore.plugin.coreshop.rules.conditions.yourCondition = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'yourCondition',

    getForm : function () {
        this.form = Ext.create('Ext.form.Panel', {
            items : [
                //Add some ExtJs Fields here
            ]
        });

        return this.form;
    }
});


```

You also need to register your new Action to CoreShop. Here you can choose which price rule type is allowed

```php
//Allows it for Product Price Rules
\CoreShop\Model\Product\PriceRule::getConditionDispatcher()->addType(\Website\Model\PriceRule\Condition\YourCondition::class);

//Allows it for Specific Prices
\CoreShop\Model\Product\SpecificPrice::getConditionDispatcher()->addType(\Website\Model\PriceRule\Condition\YourCondition::class);

//Allows it for Cart Price Rules
\CoreShop\Model\Cart\PriceRule::getConditionDispatcher()->addType(\Website\Model\PriceRule\Condition\YourCondition::class);
```

or even better:

```php
//Allows it for Product Price Rules
\Pimcore::getEventManager()->attach('coreshop.rules.productPriceRule.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Condition\YourCondition::class);
    }
});

//Allows it for Specific Prices
\Pimcore::getEventManager()->attach('coreshop.rules.specificPriceRule.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Condition\YourCondition::class);
    }
});

//Allows it for Cart Price Rules
\Pimcore::getEventManager()->attach('coreshop.rules.cartPriceRule.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Condition\YourCondition::class);
    }
});
```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)