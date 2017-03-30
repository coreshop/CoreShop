# CoreShop Upgrade Notes

Always check this page for some important upgrade notes before updating to the latest coreshop build.

## Update from Version 1.2.1 (Build 122) to Version 1.2.2 (Build 123)

**Index Service**
Index Service has been refactored. It now uses the newly introduced Composite\Dispatcher as well. This change also affects custom Interperter and custom Getter. If you use custom
Getter/Interpreter/IndexType, you need to add a static $type variable as Identifier for your custom class.

For example

```php
class YourInterpreter extends AbstractInterpreter
{
    /**
     * @var string
     */
    public static $type = 'yourInterpreter';
}
```

To add new Interpreter/Getter/IndexType use following event:

```php

//Add new Index Service Provider
\Pimcore::getEventManager()->attach('coreshop.indexService.provider.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\IndexService\YourProvider::class);
    }
});

//Add a new Getter
\Pimcore::getEventManager()->attach('coreshop.indexService.getter.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\IndexService\Getter\YourGetter::class);
    }
});

//Add a new Interpreter
\Pimcore::getEventManager()->attach('coreshop.indexService.interpreter.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Bundle\LegacyBundle\Composite\Dispatcher) {
        $target->addType(\Website\IndexService\Interpreter\YourInterpreter::class);
    }
});
```

## Update from Version 1.1.2 (Build 106) to Version 1.2 (Build 116)

**Rules**   
If you implemented some custom Rules for Product Price Rules/Cart Price Rules/Specific Price Rules or Shipping Rules, you need to change your code slightly to work again.

Only thing that is important to change: the $type variable was until 1.2 an instance variable, this has been changed to be an static variable.

You should also change how to register for Actions/Conditions. Until 1.2 it was only possible to register new types by implementing them within the CoreShop namespace.
This has been changed now, you can use any Namespace you like. For example:

If your Action had the name ```\CoreShop\Bundle\LegacyBundle\Model\PriceRule\Action\MyAction``` you could rename it to whatever you like, but you also need to register it diffently:

Until 1.2, register a new type has been done by following ```\CoreShop\Bundle\LegacyBundle\Model\Product\PriceRule::addAction('myAction');```. This is now changed to ```\CoreShop\Bundle\LegacyBundle\Model\Product\PriceRule::getActionDispatcher()->addType(\CoreShop\Bundle\LegacyBundle\Model\PriceRule\Action\MyAction::class)```.

Sometimes it happens, that pimcore loads your plugin before CoreShop is loaded, therefore we now have an event to register new Types:

```php
\Pimcore::getEventManager()->attach('coreshop.rules.productPriceRule.action.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof Dispatcher) {
        $target->addType(\CoreShop\Bundle\LegacyBundle\Model\PriceRule\Action\MyAction::class);
    }
});
```

Following Composite-Dispatcher Types are now available for registering custom types:

 - productPriceRule.action
 - productPriceRule.condition
 - specificPriceRule.action
 - specificPriceRule.condition
 - cartPriceRule.action
 - cartPriceRule.condition
 - shippingRule.action
 - shippingRule.condition
 - filter.condition
 - filter.similarty

**Order States**  
Order States has been completely removed from CoreShop 1.2. 
Instead, CoreShop is using the Pimcore Workflow to achieve flexible Order States / Statuses.

**Mail Rules**  
Normally, there is nothing special work to do. The CoreShop updater will install some valid Mail Rules for you.
Anyway, it's okay if you check the rules after the install - especially the action tab (check if all email has been placed correctly.).

**Email Template**   
All email templates obtaining a `object` parameter. Update your email templates like so:  

```html
    <!-- before -->
    <?= $this->order; ?>
    <!-- after -->
    <?= $this->object; ?>
```