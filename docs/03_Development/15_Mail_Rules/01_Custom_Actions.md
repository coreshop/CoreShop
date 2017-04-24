# TODO

#### Create Custom CoreShop Mail Rule Action

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### After 1.2

```
website/lib/Website/CoreShop/Model/Mail/Rule/Action/YourAction.php

OR

YourPlugin/lib/YourPlugin/Model/Mail/Rule/Action/YourAction.php
```

```php
namespace Website\Model\Mail\Rule\Action;

use CoreShop\Bundle\LegacyBundle\Model\Mail\Rule\Action\AbstractAction;
use CoreShop\Bundle\LegacyBundle\Model;
use Pimcore\Model\AbstractModel;

class YourAction extends AbstractAction
{
    public static $type = 'yourAction';

    public function apply(AbstractModel $model, Model\Mail\Rule $rule, $params = [])
    {
        //You can do whatever you want here, send Mail Document, or call API
        return true;
    }
}

```
If you need some configuration, you can create a JS file for the Mail Rule Action UI.

```
YourPlugin/static/js/coreshop/mail/rule/action/yourAction.js
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

You also need to register your new Action to CoreShop. You need determine which Mail - Rule Type you want to support

```php

//Support for Order type
\CoreShop\Bundle\LegacyBundle\Model\Mail\Rule::getActionDispatcherForType('order')->addType(\Website\Model\Mail\Rule\Action\YourAction::class);

//Support for Payment type
\CoreShop\Bundle\LegacyBundle\Model\Mail\Rule::getActionDispatcherForType('payment')->addType(\Website\Model\Mail\Rule\Action\YourAction::class);
```

or even better:

```php
//Support for Order type
\Pimcore::getEventManager()->attach('coreshop.rules.mailRules.order.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\Mail\Rule\Action\YourAction::class);
    }
});

//Support for Payment type
\Pimcore::getEventManager()->attach('coreshop.rules.mailRules.payment.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\Model\Mail\Rule\Action\YourAction::class);
    }
});



```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)