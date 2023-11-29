# State Management

CoreShop employs a sophisticated [State Machine](https://symfony.com/doc/current/workflow/state-machines.html) as a core
feature, enabling the management of complex workflows in a secure and modern manner. Unlike many eCommerce frameworks
that use a cumbersome and rigid "state/status" concept, CoreShop's approach offers a more dynamic and extendable state
workflow.

## Places

In the context of a State Machine, the traditional "Status" is referred to as "Places". Each workflow comes with its
predefined set of Places.

## Transition

Transitions are used to change the Place of a workflow. A transition is considered valid if it successfully updates the
workflow to a new place.

## Callbacks

Several events can be triggered for each transition, and these can be extended for specific project needs. **Example**:
When all order payments reach the `completed` place, the `coreshop_order_payment` workflow automatically transitions
to `paid`.

## Workflows

CoreShop includes seven implemented workflows:

- `coreshop_order`
- `coreshop_order_payment`
- `coreshop_order_shipment`
- `coreshop_order_invoice`
- `coreshop_payment`
- `coreshop_shipment`
- `coreshop_invoice`

These workflows are interconnected, so any transition in one can trigger transitions in others. Once a transition is
dispatched, it cannot be reversed unless such an option is defined in the available transitions.

### Explore Further:

- [Available Workflows](./01_Available_Workflows.md)
- [Create Callbacks](./02_Create_Callbacks.md)
- [Things to Know (!)](./03_Things_To_Know.md)
- [Extend Workflows](./04_Extend_Workflows.md)
