
#### Create Custom CoreShop Mail Rule Type

You simply need to attach the event 'coreshop.rules.mailRules.types.init' and return a string or an array of new types.


```php
\Pimcore::getEventManager()->attach('coreshop.rules.mailRules.types.init', function() {
    return 'notification'; //or ['notification', 'erpapi']
});
```
