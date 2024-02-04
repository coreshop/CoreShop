# Orders

## Utilizing Grid View for Order Management

CoreShop's order management leverages Pimcore's grid view, providing a robust and customizable interface.

### Default Column Configuration

To access the default column setup:

1. Click on "Column configuration" located on the right side.
2. Select "Order Overview" from the options.
3. To set this as your preferred view, click the "Column configuration" button again and choose "Set as favorite".

## Customizing Filters in Grid View

For project-specific needs, CoreShop allows the addition of custom filter definitions:

- These customizations require configuration through a PHP service.
- Detailed instructions and guidelines can be
  found [here](../../03_Development/06_Order/14_Backend_Management/01_OrderList_Filter.md).

## Implementing Bulk Actions in Grid View

CoreShop supports the application of custom bulk actions for selected orders:

- Similar to filters, these actions are set up via a PHP service.
- For more information on creating and applying these actions,
  visit [here](../../03_Development/06_Order/14_Backend_Management/02_OrderList_Action.md).
