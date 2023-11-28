# One Object to Rule Them All

CoreShop utilizes a single DataObject class for managing Orders, Carts, and Quotes. This is the `CoreShopOrder` class.

## From Cart to Order

The conversion of a Cart into an Order triggers the `SaleState` Workflow, facilitating the transition within the
CoreShop system.
