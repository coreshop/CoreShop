# CoreShop Shipment Creation

See [Order Transformer](../02_Transformer.md) for more.

## Add a Shipment to an Order

```php
/**
 * Note:
 *
 * The TRANSITION_REQUEST_SHIPMENT transition can only be applied once.
 * Only dispatch it with the creation of the first shipment.
 * This transition will inform the order shipment workflow that it's ready to initiate the shipment processing.
*/
$workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_shipment');
$workflow->apply($order, OrderShipmentTransitions::TRANSITION_REQUEST_SHIPMENT);

$order = '';

/** @var ShipmentInterface $shipment */
$shipment = $this->container->get('coreshop.factory.order_shipment')->createNew();
$shipment->setState(ShipmentStates::STATE_NEW);
$shipment = $this->get(''coreshop.order.transformer.order_to_shipment'')->transform($order, $shipment, $items);

```