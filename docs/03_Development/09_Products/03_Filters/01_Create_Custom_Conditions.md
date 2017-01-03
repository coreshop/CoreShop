
#### CoreShop Custom Filters

Create a file in your Plugin (or Website, I recommend creating a Plugin):

#### After 1.2

```
website/lib/Website/CoreShop/Model/Product/Filter/Condition/YourCondition.php

OR

YourPlugin/lib/YourPlugin/Model/Product/Filter/Condition/YourCondition.php
```

```php
namespace Website\Model\Product\Filter\Condition;

use CoreShop\IndexService\Condition;
use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

class YourCondition extends Filter\Condition\AbstractCondition
{
    public static $type = 'yourCondition';

    public function render(Filter $filter, Listing $list, $currentFilter)
    {
        $script = $this->getViewScript($filter, $list, $currentFilter);

        return $this->getView()->partial($script, [
            'label' => $this->getLabel(),
            'fieldname' => $this->getField(),
            'quantityUnit' => $this->getQuantityUnit()
        ]);
    }

    public function addCondition(Filter $filter, Listing $list, $currentFilter, $params, $isPrecondition = false)
    {
        //Add your Conditions to $list here

        return $currentFilter;
    }
}

```
If you need some configuration, you can create a JS file for the Shipping Rule UI.

```
YourPlugin/static/js/coreshop/product/filter/condition/yourCondition.js
```

```js
pimcore.registerNS('pimcore.plugin.coreshop.filters.conditions.yourCondition');

pimcore.plugin.coreshop.filters.conditions.yourCondition = Class.create(pimcore.plugin.coreshop.filters.conditions.abstract, {

    type : 'yourCondition',

    getItems : function ()
    {
        //Return Some extjs fields here
        return [

        ];
    }
});

```

You also need to register your new Action to CoreShop:

```php
\CoreShop\Model\Product\Filter::getConditionDispatcher()->addType(\Website\Model\Product\Filter\Condition\YourCondition::class);
```

or even better:

```php
\Pimcore::getEventManager()->attach('coreshop.rules.filter.condition.init', function(\Zend_EventManager_Event $e) {
    $target = $e->getTarget();

    if($target instanceof \CoreShop\Composite\Dispatcher) {
        $target->addType(\Website\Model\Product\Filter\Condition\YourCondition::class);
    }
});
```

You can find more examples in the CoreShopExamples Project [https://github.com/coreshop/CoreShopExamples](https://github.com/coreshop/CoreShopExamples)