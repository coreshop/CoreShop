# Order Detail

## State / Price Overview
Besides some useful information about the order store and the total order amount,
there're also several order states:

### State
This is the main state and represents the summary of all order states.

| Name | Description |
|:-----|:-----------|
| new | New order has been placed. |
| confirmed | New order has been successfully created (after a offline order or a customer returns from payment gateway regardless of its state). |
| cancelled | Order has been cancelled. |
| complete | Order is complete. |

#### Order Completion
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

### Payment State
Global payment states per order and represents the summary of all order payments:

| Name | Description |
|:-----|:-----------|
| new | New payment has been created. |
| awaiting_payment | Waiting for payment: User is on payment offsite or payment is offline. |
| partially_paid | Some order payments have been paid. |
| paid | All payments have been paid. |
| cancelled | Order is complete. |
| partially_refunded | Some order payments have been refunded. |
| refunded | All payments have been refunded. |

### Shipment State
Global shipment states per order and represents the summary of all order shipments:

| Name | Description |
|:-----|:-----------|
| new | New shipment has been placed. |
| cancelled | Shipment has been cancelled |
| partially_shipped | Some order items have been shipped. |
| shipped | All items have been shipped. |

### Invoice State
Global invoice states per order and represents the summary of all order invoices:

| Name | Description |
|:-----|:-----------|
| new | New invoice has been created. |
| cancelled | Invoice has been cancelled |
| partially_invoiced | Some invoices have been invoiced. |
| invoiced | All invoices have been invoiced. |


## Carrier/Payment Provider Info
- Currency of Order
- Overall weight of Order Items
- Carrier
- Total Amount

## Order History
The Order History shows you when specific states have been changed.

## Payments
--

## Shipments
--

## Invoices
--

## Mail Correspondence
--

## Customer
--

## Comments
--

## Additional Data
--

## Products
--
