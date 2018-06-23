# CoreShop Order List Bulk Actions

Bulk Actions allows you to process orders rapidly.
For Example: Apply shipment state "shipped" to all selected orders at once.

![filter](http://g.recordit.co/QCN7emXQ8L.gif)

## Register Filter Service

```yml
AppBundle\CoreShop\OrderList\Bulk\DemoBulk:
    tags:
        - { name: coreshop.order_list_bulk, type: demo }
```

## Create PHP Class

```php
<?php

namespace AppBundle\CoreShop\OrderList\Bulk;

use CoreShop\Component\Order\OrderList\OrderListBulkInterface;

class DemoBulk implements OrderListBulkInterface
{
    /**
     * The name of bulk action.
     * This value will be translated via backend translator,
     * so it's good practice to choose a symfony standard translation keys like "coreshop.order_bulk.your_bulk_name"
     * @return string
     */
    public function getName()
    {
        return 'coreshop.order_bulk.demo';
    }

    /**
     * @param array $processIds
     * @return string
     */
    public function apply(array $processIds)
    {
        $message = '';
        foreach ($processIds as $id) {
            // do your bulk work here!
            $message .= sprintf('- order with id %s successfully applied state "demo".%s', $id, '<br>');
        }

        return $message;
    }

    /**
     * Define if filter for current sale type.
     *
     * @param string $saleType
     * @return bool
     */
    public function typeIsValid($saleType = OrderListBulkInterface::SALE_TYPE_ORDER)
    {
        return $saleType === OrderListBulkInterface::SALE_TYPE_ORDER;
    }
}
```
