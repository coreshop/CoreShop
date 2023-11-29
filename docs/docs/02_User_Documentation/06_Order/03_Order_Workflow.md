# Order Workflow

CoreShop provides a comprehensive workflow system for managing various states of orders, payments, shipments, and
invoices. For a technical overview, refer to
the [State Management documentation](../../03_Development/06_Order/16_State_Management/index.md).

## Changing States

States can be changed for Orders, Payments, Shipments, and Invoices. If a transition is available, a colored state
button will be displayed. Click this button to change the state.

## Order Completion

In CoreShop, an order is considered complete when the payment state reaches `paid` and the shipment state
reaches `shipped`. If you wish to include the invoice state `invoiced` in the order completion criteria, this can be
enabled via the config like this:

```yml
## app/config/config.yml
parameters:
    coreshop.workflow.include_invoice_state_to_complete_order: true
```

#### Order Cancellation

Orders are rarely cancelled in CoreShop, except under specific circumstances:

- **Front-End Review**: After a customer cancels their payment, they reach a review page where they can restart the
  payment process or cancel the order and restore their cart.
- **Back-End Review**: Similar to the front-end, but the customer cannot restore a cart since the order was created in
  the back-end.

More details on the cancellation process can be
found [here](../../03_Development/06_Order/16_State_Management/03_Things_To_Know.md).

## Payment Workflow

### Creating a Payment

Create a payment using the green button in the top-right corner. Payment state changes are typically managed by the
frontend payment gateway.

### Workflow Rules

- Payments enter the `new` state upon creation.
- Payments can be cancelled as long as they are in the `new` or `processing` state.
- Once a payment reaches the `completed` state, it can only be refunded.

> **Attention**: The refund process is not yet implemented in CoreShop!

## Shipment Workflow

### Creating a Shipment

Create a shipment using the green button in the top-right corner, provided there are enough shippable items.

### Workflow Rules

- Shipments enter the `ready` state after creation.
- Shipments can be cancelled as long as they are in the `ready` state.
- Once the `ship` state is triggered, a shipment cannot be cancelled anymore.

## Invoice Workflow

### Creating an Invoice

Create an invoice using the green button in the top-right corner, if there are enough invoiceable items.

### Workflow Rules

- Invoices enter the `ready` state after creation.
- Invoices can be cancelled as long as they are in the `ready` state. **Note**: Cancelling an invoice is not a refund
  action. You are allowed to create another invoice after cancelling the previous one.
- Once the `complete` state is triggered, an invoice cannot be cancelled anymore, which means the invoice has been
  finally captured. After that, a refund process must be initiated.

> **Attention**: The refund process is not yet implemented in CoreShop!
