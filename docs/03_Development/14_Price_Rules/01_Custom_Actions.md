
#### Create Custom CoreShop Price Rule Action

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### After 1.2

```
website/lib/Website/CoreShop/Model/PriceRule/Action/YourAction.php

OR

YourPlugin/lib/YourPlugin/Model/PriceRule/Action/YourAction.php
```

```php
namespace Website\Model\PriceRule\Action;

use CoreShop\Bundle\LegacyBundle\Model\Cart;
use CoreShop\Bundle\LegacyBundle\Model\PriceRule\Action\AbstractAction;
use CoreShop\Bundle\LegacyBundle\Model\Product;

class YourAction extends AbstractAction
{
    public static $type = 'yourAction';

    public function applyRule(Cart $cart)
    {
        //You can do some Cart operations here, like adding an gift product
        return true;
    }

    public function unApplyRule(Cart $cart)
    {
        //You can do some Cart operations here, like removing an gift product
        return true;
    }

    public function getDiscountCart(Cart $cart, $withTax = true)
    {
        //Applies 10% discount on the cart
        return $subTotalTe = $cart->getSubtotal(false) * 0.1;
    }

    public function getDiscountProduct($basePrice, Product $product)
    {
        //Applies 10% discount on the product
        return $basePrice * 0.1;
    }
}

```
If you need some configuration, you can create a JS file for the Shipping Rule UI.

```
YourPlugin/static/js/coreshop/pricerules/action/yourAction.js
```

```js

pimcore.registerNS('pimcore.plugin.coreshop.rules.actions.yourAction');

pimcore.plugin.coreshop.rules.actions.yourAction = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'yourAction',

    getForm : function () {
        this.form = new Ext.form.Panel({
            items : [
                //Add Some ExtJs Fields here
            ]
        });

        return this.form;
    }
});

```

You also need to register your new Action to CoreShop. Here you can choose which price rule type is allowed

```php
//Allows it for Product Price Rules
\CoreShop\Bundle\LegacyBundle\Model\Product\PriceRule::getActionDispatcher()->addType(\Website\Model\PriceRule\Action\YourAction::class);

//Allows it for Specific Prices
\CoreShop\Bundle\LegacyBundle\Model\Product\SpecificPrice::getActionDispatcher()->addType(\Website\Model\PriceRule\Action\YourAction::class);

//Allows it for Cart Price Rules
\CoreShop\Bundle\LegacyBundle\Model\Cart\PriceRule::getActionDispatcher()->addType(\Website\Model\PriceRule\Action\YourAction::class);
```

or even better:

```php
//Allows it for Product Price Rules
\Pimcore::getEventManager()->attach('coreshop.rules.productPriceRule.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Action\YourAction::class);
    }
});

//Allows it for Specific Prices
\Pimcore::getEventManager()->attach('coreshop.rules.specificPriceRule.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Action\YourAction::class);
    }
});

//Allows it for Cart Price Rules
\Pimcore::getEventManager()->attach('coreshop.rules.cartPriceRule.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\PriceRule\Action\YourAction::class);
    }
});

```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)