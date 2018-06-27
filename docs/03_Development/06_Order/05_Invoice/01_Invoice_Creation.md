# CoreShop Invoice Creation

See [Order Transformer](../02_Transformer.md) for more.


## Add a Invoice to an Order

```php
/**
 * Note:
 *
 * The TRANSITION_REQUEST_INVOICE transition can only be applied once.
 * Only dispatch it with the creation of the first shipment.
 * This transition will inform the order invoice workflow that it's ready to initiate the invoice processing.
*/
$workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_invoice');
$workflow->apply($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE);

$order = '';

/** @var ShipmentInterface $shipment */
$invoice = $this->container->get('coreshop.factory.order_invoice')->createNew();
$invoice->setState(InvoiceStates::STATE_NEW);
$invoice = $this->get(''coreshop.order.transformer.order_to_invoice'')->transform($order, $shipment, $items);

```