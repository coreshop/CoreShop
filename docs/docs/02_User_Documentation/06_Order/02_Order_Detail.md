# Order Detail

The Order Detail section in CoreShop provides comprehensive information about individual orders, including their states,
price overview, and other relevant details.

## State and Price Overview

For an in-depth understanding of the order workflow process, refer to
the [Order Workflow documentation](./03_Order_Workflow.md).

### Key Information

- **Order Store**: Indicates the store where the order was placed.
- **Total Order Amount**: The complete amount for the order.

### Order States

#### Main State

The primary state summarizing the overall status of the order.

| Name      | Description                 |
|:----------|:----------------------------|
| new       | Newly placed order.         |
| confirmed | Order successfully created. |
| cancelled | Order has been cancelled.   |
| complete  | Order is complete.          |

#### Payment State

Reflects the overall payment status of the order.

| Name               | Description                    |
|:-------------------|:-------------------------------|
| new                | New payment initiated.         |
| awaiting_payment   | Awaiting payment confirmation. |
| partially_paid     | Partial payment received.      |
| paid               | Full payment received.         |
| cancelled          | Payment cancelled.             |
| partially_refunded | Partial refund processed.      |
| refunded           | Full refund processed.         |

#### Shipment State

Indicates the shipping status of the order.

| Name              | Description                 |
|:------------------|:----------------------------|
| new               | New shipment initiated.     |
| cancelled         | Shipment cancelled.         |
| partially_shipped | Partial shipment completed. |
| shipped           | Full shipment completed.    |

#### Invoice State

Summarizes the invoicing status of the order.

| Name               | Description                  |
|:-------------------|:-----------------------------|
| new                | New invoice created.         |
| cancelled          | Invoice cancelled.           |
| partially_invoiced | Partial invoicing completed. |
| invoiced           | Full invoicing completed.    |

## Carrier and Payment Provider Information

- **Currency**: The currency used for the order.
- **Total Weight**: The cumulative weight of all order items.
- **Carrier**: The shipping provider used.
- **Total Amount**: The total amount for the order.

## Additional Sections

- **Order History**: Tracks changes in order states.
- **Payments**: Details about payment transactions.
- **Shipments**: Information on shipment progress.
- **Invoices**: Records of invoicing.
- **Mail Correspondence**: Log of emails related to the order.
- **Customer**: Customer details.
- **Comments**: Any comments or notes on the order.
- **Products**: List of products included in the order.
