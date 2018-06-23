# CoreShop Order List Bulk Actions

Bulk Actions allows you to process orders rapidly, right in the order grid view.

![filter](http://g.recordit.co/QCN7emXQ8L.gif)

## Register Filter Service

```yml
AppBundle\CoreShop\OrderList\Bulk\DemoBulk:
    arguments:
        $stateMachineManager: '@coreshop.state_machine_manager'
        $shipmentRepository: '@coreshop.repository.order_shipment'
    tags:
        - { name: coreshop.order_list_bulk, type: demo }
```

## Create PHP Class
In this example we want to apply the shipment transition "ship" to selected orders.

```php
<?php

namespace AppBundle\CoreShop\OrderList\Bulk;

use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\OrderList\OrderListBulkInterface;
use Pimcore\Model\DataObject\CoreShopOrder;

class DemoBulk implements OrderListBulkInterface
{
    protected $stateMachineManager;

    protected $shipmentRepository;

    public function __construct(
        StateMachineManagerInterface $stateMachineManager,
        OrderShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->stateMachineManager = $stateMachineManager;
        $this->shipmentRepository = $shipmentRepository;
    }

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
        $transition = 'ship';
        $shipmentIds = [];

        foreach ($processIds as $id) {

            $m = [];
            $order = CoreShopOrder::getById($id);
            $shipments = $this->shipmentRepository->getDocuments($order);

            if (count($shipments) === 0) {
                $m[] = sprintf('- no shipments for order %s found. skipping....', $order->getId());
            } else {
                foreach ($shipments as $shipment) {
                    if ($shipment->getState() === 'shipped') {
                        $m[] = sprintf('- transition "%s" for shipment %s already applied. skipping...', $transition, $shipment->getId());
                        continue;
                    }
                    $workflow = $this->stateMachineManager->get($shipment, 'coreshop_shipment');
                    if (!$workflow->can($shipment, $transition)) {
                        $m[] = sprintf('- transition "%s" for shipment %s not allowed.', $transition, $shipment->getId());
                    } else {
                        try {
                            $workflow->apply($shipment, $transition);
                            $shipmentIds[] = $shipment->getId();
                            $m[] = sprintf('- transition "%s" for shipment id %s successfully applied.', $transition, $shipment->getId());
                        } catch (\Exception $e) {
                            $m[] = sprintf('- error while applying transition "%s" to shipment with id %s: %s.', $transition, $shipment->getId(), $e->getMessage());
                        }
                    }
                }
            }

            $message .= sprintf('<strong>Order %s:</strong><br>%s<br>', $id, join('<br>', $m));

        }

        if (count($shipmentIds) > 0) {
            $packingListUrl = '/admin/your-packing-list-generator-url?ids=' . join(',', $shipmentIds);
            $message .= sprintf('<br><a href="%s" target="_blank">%s</a><br>', $packingListUrl, 'packing list');
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
