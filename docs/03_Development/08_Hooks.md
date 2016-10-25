# CoreShop Hooks (Events)

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

## All Hooks

| Hook Name | Params | Description |
|---|---|---|
| coreshop.install.post | CoreShop\Plugin\Install | Fired after installing CoreShop |
| coreshop.uninstall.pre | CoreShop\Plugin\Install | Fired before uninstalling CoreShop |
| coreshop.uninstall.post | CoreShop\Plugin\Install | Fired after uninstalling CoreShop |
| coreshop.controller.init | CoreShop\Controller\Action | Fired after CoreShop Controller Initialization |
| coreshop.tax.getTaxManager | CoreShop\Model\User\Address, int $taxRuleId | Fired to get the appropriate TaxManager for a address |
| coreshop.install.class.getClass.[CLASSNAME] | string $className, string $json | Fired to get the ClassDefinition for a ClassName |
| coreshop.install.class.preCreate | string $className, string $json | Fired before a CoreShop Class is saved |
| coreshop.install.objectbrick.preCreate | string $className, string $json | Fired before a CoreShop Objectbrick is saved |
| coreshop.install.fieldcollection.preCreate | string $className, string $json | Fired before a CoreShop Fieldcollection is saved |
