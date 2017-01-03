
#### CoreShop Custom Shipping Rule Condition

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### Before 1.2

```
YourPlugin/lib/CoreShop/Model/Carrier/ShippingRule/Condition/YourCondition.php
```

```php
namespace CoreShop\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

class YourCondition extends AbstractCondition
{
    public $type = 'yourCondition';

    public function checkCondition(Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule) {
        //Do something here to check the condition.

        return true;
    }
}
```

You also need to create a js file for the ShippingRule UI.

```
YourPlugin/static/js/coreshop/carrier/shippingRule/condition/yourCondition.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.conditions.yourCondition');

pimcore.plugin.coreshop.carrier.shippingrules.conditions.yourCondition = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'yourCondition',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                //add some extjs fields here
            ]
        });

        return this.form;
    }
});

```

You also need to register your new Condition to CoreShop:

```php
\CoreShop\Model\Carrier\ShippingRule::addCondition("YourCondition");
```

#### After 1.2

```
website/lib/Website/Model/Carrier/ShippingRule/Condition/YourCondition.php

OR

YourPlugin/lib/YourPlugin/Model/Carrier/ShippingRule/Condition/YourCondition.php
```

```php
namespace Website\Model\Carrier\ShippingRule\Condition;

use CoreShop\Model;
use CoreShop\Model\Carrier\ShippingRule;
use CoreShop\Tool;

class YourCondition extends AbstractCondition
{
    public static $type = 'yourCondition';

    public function checkCondition(Model\Cart $cart, Model\User\Address $address, ShippingRule $shippingRule) {
        //Do something here to check the condition.

        return true;
    }
}
```

You also need to create a js file for the ShippingRule UI.

```
YourPlugin/static/js/coreshop/carrier/shippingRule/condition/yourCondition.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.carrier.shippingrules.conditions.yourCondition');

pimcore.plugin.coreshop.carrier.shippingrules.conditions.yourCondition = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'yourCondition',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                //add some extjs fields here
            ]
        });

        return this.form;
    }
});

```

You also need to register your new Condition to CoreShop:

```php
\CoreShop\Model\Carrier\ShippingRule::getConditionDispatcher()->addType(\Website\Model\Carrier\ShippingRule\Action\YourCondition::class);
```

or even better:

```php
\Pimcore::getEventManager()->attach('coreshop.rules.shippingRule.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Composite\Dispatcher) {
        $target->addType(\Website\Model\Carrier\ShippingRule\Action\YourAction::class);
    }
});
```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)