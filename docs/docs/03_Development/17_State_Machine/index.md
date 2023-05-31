# CoreShop State Machine

The CoreShop [State Machine](https://symfony.com/doc/current/workflow/state-machines.html) is a important core feature which allows to determinate complex workflows securely and in a most modern way.
Unlike the most eCommerce Frameworks out there, CoreShop does not work with the messy and hard do extend "state/status" concept.
Every order-section and of course the order itself provides its own state machine which allows us to build a super strong state workflow.

## Places
In State Machine context, the well-known "Status" Property is called "Places".
Every Workflow comes with a pre-defined set of Places.

## Transition
To change the Place of a workflow we need to apply a transition.
If the transition is valid the new place gets stored.

## Callbacks
There are several events for each transition which can be also extend by every project.
**Example:**: If all order payments has been successfully transformed to the `completed` place,
the `coreshop_order_payment` workflow will automatically change to `paid`.

## Workflows

There are seven implemented Workflows:

- `coreshop_order`
- `coreshop_order_payment`
- `coreshop_order_shipment`
- `coreshop_order_invoice`
- `coreshop_payment`
- `coreshop_shipment`
- `coreshop_invoice`

Workflows are connected among themselves so every transition will trigger another Workflow and so on.
If a transition has been dispatched, it cannot be transformed back unless it has been defined in the available transitions.

So let's start:

 - [Available Workflows](./01_Available_Workflows.md)
 - [Create Callbacks](./02_Create_Callbacks.md)
 - [Things to Know (!)](./03_Things_To_Know.md)
 - [Extend Workflows](./04_Extend_Workflows.md)
