# Order Workflow

> [Technical Overview](../../03_Development/17_State_Machine/README.md) of CoreShop Workflows.

## Change States
States can be changed for: Order, Payment, Shipment or Invoice.
If any transition is available, you'll find a colored state button.
Click on that button to change the state.

## Complete Order
A Order is complete when:
- All payments has been changed to `paid`.
- All shipments has been changed to `shipped`.