# CoreShop Order List Filter

This comes in handy if you need some special filtered data in your Order List.
Your Customer is able to filter orders on specific conditions.

![filter](http://g.recordit.co/ciLUUskSxX.gif)

## Register Filter Service

```yml
AppBundle\CoreShop\OrderList\Filter\DemoFilter:
    tags:
        - { name: coreshop.grid.filter, type: demo }
```

## Create PHP Class
In this example we want to filter orders with available shipments in state "ready".

```php
<?php

namespace AppBundle\CoreShop\OrderList\Filter;

use CoreShop\Component\Pimcore\DataObject\Grid\GridFilterInterface;
use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\DataObject;

class DemoFilter implements GridFilterInterface
{
    public function getName()
    {
        return 'coreshop.order_filter.shipment_apply';
    }

    public function filter(DataObject\Listing $list, array $context)
    {
        $list->onCreateQuery(function (QueryBuilder $select) use ($list) {
            $select->join(
                ['shipment' => 'object_query_4'],
                'shipment.order__id = object_' . $list->getClassId() . '.o_id'
            );
        });

        $list->addConditionParam('orderState = ?', 'confirmed');
        $list->addConditionParam('shipment.state = ?', 'ready');

        return $list;
    }

    public function supports($listType)
    {
        return $listType === 'coreshop_order';
    }
}
```