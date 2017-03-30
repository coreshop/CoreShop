
#### Create Custom CoreShop Mail Rule Conditions

Mail Rule Conditions are seperated into different types, for example Order, Payment or Invoice. Conditions are only valid for one type.
In this example we are creating a new Condition for type "order";

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### After 1.2

```
website/lib/Website/CoreShop/Model/Mail/Rule/Condition/Order/YourCondition.php

OR

YourPlugin/lib/YourPlugin/Model/Mail/Rule/Condition/Order/YourCondition.php
```

```php
namespace Website\Model\Mail\Rule\Condition\Order;

use CoreShop\Bundle\LegacyBundle\Model;
use CoreShop\Bundle\LegacyBundle\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

class YourCondition extends Rule\Condition\AbstractCondition
{
   public static $type = 'yourCondition';

   public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
   {
       //Return TRUE if valid, otherwise FALSE
       return true;
   }
}

```
If you need some configuration, you can create a JS file for the Mail Rule Condition UI.

```
YourPlugin/static/js/coreshop/mail/rule/condition/order/yourCondition.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.conditions.yourCondition');

pimcore.plugin.coreshop.mail.rules.conditions.yourCondition = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
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

You also need to register your new Condition to CoreShop.

```php
\CoreShop\Bundle\LegacyBundle\Model\Mail\Rule::getConditionDispatcherForType('order')->addType(\Website\Model\Mail\Rule\Condition\Order\YourCondition::class);
```

or even better:

```php
\Pimcore::getEventManager()->attach('coreshop.rules.mailRules.order.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\Mail\Rule\Action\YourAction::class);
    }
});

```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)