# Invoice Creation

## Add a Invoice to an Order

```php
/**
 * Note:
 *
 * The TRANSITION_REQUEST_INVOICE transition can only be applied once.
 * Only dispatch it with the creation of the first invoice.
 * This transition will inform the order invoice workflow that it's ready to initiate the invoice processing.
*/
$workflow = $this->getStateMachineManager()->get($order, 'coreshop_order_invoice');
$workflow->apply($order, OrderInvoiceTransitions::TRANSITION_REQUEST_INVOICE);

$order = '';

/** @var InvoiceInterface $invoice */
$invoice = $this->container->get('coreshop.factory.order_invoice')->createNew();
$invoice->setState(InvoiceStates::STATE_NEW);

$items = [];
$invoice = $this->get('coreshop.order.transformer.order_to_invoice')->transform($order, $invoice, $items);
```