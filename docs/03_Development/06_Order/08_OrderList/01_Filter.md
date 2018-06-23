# CoreShop Order List Filter

This comes in handy if you need some special filtered data in your Order List.
Your Customer is able to filter orders on specific conditions.

![filter](http://g.recordit.co/ciLUUskSxX.gif)

## Register Filter Service

```yml
AppBundle\CoreShop\OrderList\Filter\DemoFilter:
    tags:
        - { name: coreshop.order_list_filter, type: demo }
```

## Create PHP Class
In this example we want to filter orders with available shipments in state "ready".

```php
<?php

namespace AppBundle\CoreShop\OrderList\Filter;

use CoreShop\Component\Order\OrderList\OrderListFilterInterface;
use Pimcore\Db\ZendCompatibility\QueryBuilder;
use Pimcore\Model\DataObject;

class DemoFilter implements OrderListFilterInterface
{

    /**
     * The name of filter action.
     * This value will be translated via backend translator,
     * so it's good practice to choose a symfony standard translation keys like "coreshop.order_filter.your_filter_name".
     *
     * @return string
     */
    public function getName()
    {
        return 'coreshop.order_filter.shipment_apply';
    }

    /**
     * @param DataObject\Listing $list
     * @param array              $context
     * @return DataObject\Listing
     */
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

    /**
     * Define if filter for current sale type.
     *
     * @param string $saleType
     * @return bool
     */
    public function typeIsValid($saleType = OrderListFilterInterface::SALE_TYPE_ORDER)
    {
        return $saleType === OrderListFilterInterface::SALE_TYPE_ORDER;
    }
}
```