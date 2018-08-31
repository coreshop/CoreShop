# Order Workflow

> [Technical Overview](../../03_Development/17_State_Machine/README.md) of CoreShop Workflows.

## Change States
States can be changed for: Order, Payment, Shipment and Invoice.
If any transition is available, you'll find a colored state button.
Click on that button to change the state.

## Order Completion
In CoreShop a order is complete after the order payment reaches the `paid` state
and order shipment reaches the `shipped` state. If you also want to include the order invoice state `invoiced`
before a order gets completed, you need to enable a configuration flag:

```yml
## app/config/config.yml
parameters:
    coreshop.workflow.include_invoice_state_to_complete_order: true
```

#### Order Cancellation
In CoreShop a order rarely gets cancelled. Some reasons are:

**Front-End Revise**
After a customer has cancelled his payment he will reach a so called revise page.
From there he's able to reselect a payment gateway to start over. In revise mode, however, it's possible to cancel the order and restore the cart.
Only than the order gets cancelled.

**Back-End Revise**
In CoreShop it's possible to create orders in Back-End. Instead of adding sensitive payment information,
you're able to provide a revise link to your customer which then works the same as the Front-End revise process
(except that your customer is not able to restore a cart since there never was one).

Please read more about the canceling process [here](../../03_Development/17_State_Machine/03_Things_To_Know.md)

## Payment Workflow

### Create a Payment
Just use the green bottom on the right top corner to add a new shipment.
A payment creation normally gets created by a frontend payment gateway.
This gateway also handles all the payment state changes.

### Workflow Rules
- A payment reaches the `new` state after its creation.
- A payment can be canceled as long it's in the `new` or `processing` state.
- After a payment reaches `completed` state it only can get refunded.

> **Attention**: The Refund process is not implemented yet in Coreshop!

## Shipment Workflow

### Create a Shipment
Just use the green bottom on the right top corner to add a new shipment.
If there are enough shippable items you can create a shipment.

### Workflow Rules
- A shipment reaches the `ready` state (triggered by a `create` transition) after its creation.
- A shipment can be canceled as long it's in the `ready` state
- After you provoked the `ship` state a shipment can't be cancelled anymore.

## Invoice Workflow

### Create Invoice
Just use the green bottom on the right top corner to add a new invoice.
If there are enough invoiceable items you can create a invoice.

### Workflow Rules
- A invoice reaches the `ready` state (triggered by a `create` transition) after its creation.
- A invoice can be canceled as long it's in the `ready` state. **Please note**: Cancelling an invoice is not a refund action. You're allowed to create another invoice after cancelling the previous one.
- After you provoked the `complete` state a invoice can't be cancelled anymore which means the invoice has been finally captured. After that you need to go on with a refund process

> **Attention**: The Refund process is not implemented yet in Coreshop!
