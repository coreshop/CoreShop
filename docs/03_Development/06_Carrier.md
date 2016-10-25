# CoreShop Carrier

CoreShop already has a feature for Carriers. But if you need some different shipping cost calculation, you can implement your own Carrier Plugin. 

To implement a custom Carrier, you need to:

1. Implement the class CoreShop\Model\Carrier. 
2. Create a Carrier in CoreShop and fill the "class" column in the database table "coreshop_carriers".

You can find a example implementation [here](https://github.com/coreshop/coreshop-carrier-custom)

## Shipping Rules
Shipping Rules are designed to be adaptable and changeable.

### Add your own Conditions/Actions
Shipping Rules are designed to be extendable. You can add your own conditions or actions for shipping price calculation.

#### Example Condition

```php
<?php
//Random.php

namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

class Random extends AbstractCondition
{
    public $type = 'random';

    public function checkCondition(Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule) {
        return (bool)random_int(0, 1);
    }
}

```

```js
//random.js
pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.conditions.random');

pimcore.plugin.coreshop.carrier.shippingrules.conditions.random = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'random',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [

            ]
        });

        return this.form;
    }
});

```

```php
<?php
//Your Plugin.php
class Plugin extends PluginLib\AbstractPlugin implements PluginLib\PluginInterface
{
    public function init()
    {
        parent::init();

        ShippingRule::addCondition("random");
    }
}
```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)