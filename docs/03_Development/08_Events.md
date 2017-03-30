# CoreShop Events

## Controller Hooks
Current available Action Hooks:
 - Cart
    - cart.preAdd
    - cart.postAdd
    - cart.preRemove
    - cart.postRemove
    - cart.preModify
    - cart.postModify
 - Order State
    - orderstate.process.post
 - Order
    - order.created
 - User
    - user.postAdd
 - Install
    - install.post
 - Controller
    - controller.init


## Action Hooks
 - actionHook.country

You can hook into any action by returning a function in the EventManager.

```
Pimcore::getEventManager()->attach("actionHook.country", function() {
    return function() {
        return Country::getById(3);
    };
});
```

## All Events

| Event Name | Params | Description |
|---|---|---|
| coreshop.install.post | CoreShop\Bundle\LegacyBundle\\Plugin\\Install | Fired after installing CoreShop |
| coreshop.uninstall.pre | CoreShop\Bundle\LegacyBundle\\Plugin\\Install | Fired before uninstalling CoreShop |
| coreshop.uninstall.post | CoreShop\Bundle\LegacyBundle\\Plugin\\Install | Fired after uninstalling CoreShop |
| coreshop.controller.init | CoreShop\Bundle\LegacyBundle\\Controller\\Action | Fired after CoreShop Controller Initialization |
| coreshop.tax.getTaxManager | CoreShop\Bundle\LegacyBundle\\Model\\User\\Address, int $taxRuleId | Fired to get the appropriate TaxManager for a address |
| coreshop.install.class.getClass.[CLASSNAME] | string $className, string $json | Fired to get the ClassDefinition for a ClassName |
| coreshop.install.class.preCreate | string $className, string $json | Fired before a CoreShop Class is saved |
| coreshop.install.objectbrick.preCreate | string $className, string $json | Fired before a CoreShop Objectbrick is saved |
| coreshop.install.fieldcollection.preCreate | string $className, string $json | Fired before a CoreShop Fieldcollection is saved |
| coreshop.rules.productPriceRule.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Actions |
| coreshop.rules.productPriceRule.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Conditions |
| coreshop.rules.specificPriceRule.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Actions |
| coreshop.rules.specificPriceRule.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Conditions |
| coreshop.rules.cartPriceRule.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Actions |
| coreshop.rules.cartPriceRule.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Price Rule Conditions |
| coreshop.rules.shippingRule.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Shipping Rule Actions |
| coreshop.rules.shippingRule.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Shipping Rule Conditions |
| coreshop.rules.filter.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Filter Conditions |
| coreshop.rules.filter.similarity.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Filter Similarities |
| coreshop.rules.mailRule.order.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.order.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.invoice.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.invoice.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.shipment.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.shipment.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.user.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.user.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.messaging.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.messaging.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.payment.action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Type |
| coreshop.rules.mailRule.payment.condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Type |
| coreshop.rules.mailRule.[CUSTOM-TYPE].action.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Actions for Custom Types |
| coreshop.rules.mailRule.[CUSTOM-TYPE].condition.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Mail Rule Conditions for Custom Types |
| coreshop.indexService.provider.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Index Service Provider |
| coreshop.indexService.getter.init | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Index Service Getter |
| coreshop.indexService.getter.interpreter | \\CoreShop\Bundle\LegacyBundle\\Composite\\Dispatcher | Fired to initialize Index Service Interpreter |