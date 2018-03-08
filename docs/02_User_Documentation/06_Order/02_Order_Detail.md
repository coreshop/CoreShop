# Order Detail

## State / Price Overview

Please read more about the order workflow process [here](./03_Order_Workflow.md)

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
