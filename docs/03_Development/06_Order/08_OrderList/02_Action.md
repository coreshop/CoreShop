# CoreShop Order List Actions

Actions allows you to process orders rapidly, right in the order grid view.

![filter](http://g.recordit.co/QCN7emXQ8L.gif)

## Register Filter Service

```yml
AppBundle\CoreShop\OrderList\Action\Demo:
    arguments:
        $stateMachineManager: '@coreshop.state_machine_manager'
        $shipmentRepository: '@coreshop.repository.order_shipment'
    tags:
        - { name: coreshop.grid.action, type: demo }
```

## Create PHP Class
In this example we want to apply the shipment transition "ship" to selected orders.

```php
<?php

namespace AppBundle\CoreShop\OrderList\Action;

use CoreShop\Component\Order\Repository\OrderShipmentRepositoryInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Pimcore\DataObject\Grid\GridActionInterface;
use Pimcore\Model\DataObject\CoreShopOrder;

class DemoAction implements GridActionInterface
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

    public function getName()
    {
        return 'coreshop.order.demo';
    }

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

    public function supports($listType)
    {
        return $listType === 'coreshop_order';
    }
}
```
