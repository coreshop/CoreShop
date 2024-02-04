# Events

CoreShop is equipped with a wide array of built-in events across different functionalities.

## Frontend Controller Events

Events triggered during frontend controller operations:

| Name                                        | EventType               | Description                                      |
|:--------------------------------------------|:------------------------|:-------------------------------------------------|
| `coreshop.customer.update_post`             | ResourceControllerEvent | Fires after customer profile update              |
| `coreshop.customer.change_password_post`    | ResourceControllerEvent | Fires after customer password change             |
| `coreshop.customer.newsletter_confirm_post` | ResourceControllerEvent | Fires after newsletter subscription confirmation |
| `coreshop.address.add_post`                 | ResourceControllerEvent | Fires after adding a new address                 |
| `coreshop.address.update_post`              | ResourceControllerEvent | Fires after updating an address                  |
| `coreshop.address.delete_pre`               | ResourceControllerEvent | Fires before deleting an address                 |

## Cart Events

Events related to cart operations:

| Name                             | EventType    | Description                             |
|:---------------------------------|:-------------|:----------------------------------------|
| `coreshop.cart.update`           | GenericEvent | Fires after cart update                 |
| `coreshop.cart.pre_add_item`     | GenericEvent | Fires before adding an item to cart     |
| `coreshop.cart.post_add_item`    | GenericEvent | Fires after adding an item to cart      |
| `coreshop.cart.pre_remove_item`  | GenericEvent | Fires before removing an item from cart |
| `coreshop.cart.post_remove_item` | GenericEvent | Fires after removing an item from cart  |

## Customer Events

Events triggered during customer-related processes:

| Name                                       | EventType                  | Description                                   |
|:-------------------------------------------|:---------------------------|:----------------------------------------------|
| `coreshop.customer.register`               | CustomerRegistrationEvent  | Fires after new customer registration         |
| `coreshop.customer.request_password_reset` | RequestPasswordChangeEvent | Fires after password reset request            |
| `coreshop.customer.password_reset`         | GenericEvent               | Fires after applying new password to customer |

## Order Document Events

Events for order document operations:

| Name                                     | EventType          | Description                                            |
|:-----------------------------------------|:-------------------|:-------------------------------------------------------|
| `coreshop.order.shipment.wkhtml.options` | WkhtmlOptionsEvent | Options Event: Modify wkhtml options for shipment docs |
| `coreshop.order.invoice.wkhtml.options`  | WkhtmlOptionsEvent | Options Event: Modify wkhtml options for invoice docs  |

## Payment Events

Events associated with payment processing:

| Name                                 | EventType                    | Description                                       |
|:-------------------------------------|:-----------------------------|:--------------------------------------------------|
| `coreshop.payment_provider.supports` | PaymentProviderSupportsEvent | Support Event: Modify available Payment Providers |

## Notes Events

Events related to note creation and deletion:

| Name                          | EventType    | Description                                   |
|:------------------------------|:-------------|:----------------------------------------------|
| `coreshop.note.*.post_add`    | GenericEvent | Fires after creating a note of specified type |
| `coreshop.note.*.post_delete` | GenericEvent | Fires after deleting a note of specified type |

Replace `*` with a note type (e.g., `payment`, `order_comment`).

## Rules Events

Events for rule operations:

| Name                               | EventType                  | Description                                       |
|:-----------------------------------|:---------------------------|:--------------------------------------------------|
| `coreshop.rule.availability_check` | RuleAvailabilityCheckEvent | Fires for every active rule in availability check |

## Workflow Events

Events for workflow transitions. Read more about it [here](../06_Order/16_State_Management/index.md).

## Backend Controller Events

Events triggered in various backend operations:

| Name                     | EventType               | Description                                |
|:-------------------------|:------------------------|:-------------------------------------------|
| `coreshop.*.pre_create`  | ResourceControllerEvent | Fires before creating an object in backend |
| `coreshop.*.post_create` | ResourceControllerEvent | Fires after creating an object in backend  |
| `coreshop.*.pre_save`    | ResourceControllerEvent | Fires before saving an object in backend   |
| `coreshop.*.post_save`   | ResourceControllerEvent | Fires after saving an object in backend    |
| `coreshop.*.pre_delete`  | ResourceControllerEvent | Fires before deleting an object in backend |
| `coreshop.*.post_delete` | ResourceControllerEvent | Fires after deleting an object in backend  |

Replace `*` with a controller type (e.g., `configuration`, `payment_provider`).

## Model Events

Pimcore Events can be used for CoreShop's Pimcore Models. Learn more
at [Pimcore Events](https://pimcore.com/docs/platform/Pimcore/Extending_Pimcore/Event_API_and_Event_Manager).
