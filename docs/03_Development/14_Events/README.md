# CoreShop Events

CoreShop comes with a lot of build-in events.

## Frontend Controller

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.customer.update_post` | ResourceControllerEvent | Fires after Customer has updated the profile |
| `coreshop.customer.change_password_post` | ResourceControllerEvent | Fires after Customer has changed the password |
| `coreshop.customer.newsletter_confirm_post` | ResourceControllerEvent | Fires after Customer has confirmed his newsletter subscription |
| `coreshop.address.add_post` | ResourceControllerEvent | Fires after Customer has added a new address |
| `coreshop.address.update_post` | ResourceControllerEvent | Fires after Customer has updated a address |
| `coreshop.address.delete_pre` | ResourceControllerEvent | Fires before Customer deletes a address |

## Cart

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.cart.update` | GenericEvent | Fires after cart has been updated |
| `coreshop.cart.pre_add_item` | GenericEvent | Fires before a item gets added to cart |
| `coreshop.cart.post_add_item` | GenericEvent | Fires after a item gets added to cart |
| `coreshop.cart.pre_remove_item` | GenericEvent | Fires before a item gets removed from cart |
| `coreshop.cart.post_remove_item` | GenericEvent | Fires after a item gets removed from cart |

## Customer

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.customer.register` | CustomerRegistrationEvent | Fires after a new customer has been generated |
| `coreshop.customer.request_password_reset` | RequestPasswordChangeEvent | Fires after password reset has been requested |
| `coreshop.customer.password_reset` | GenericEvent | Fires after new password hast been applied to customer |

## Order Document

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.order.shipment.wkhtml.options` | WkhtmlOptionsEvent | Options Event: Use it to change wkhtml options. |
| `coreshop.order.invoice.wkhtml.options` | WkhtmlOptionsEvent | Options Event: Use it to change wkhtml options. |

## Payment

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.payment_provider.supports` | PaymentProviderSupportsEvent | Support Event: Use it to modify available Payment Providers |

## Notes

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.note.*.post_add` | GenericEvent | Fires after a note of type `*` has been created |
| `coreshop.note.*.post_delete` | GenericEvent | Fires after a note of type `*` has been deleted |

## Rules

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.rule.availability_check` | RuleAvailabilityCheckEvent | Fires in `RuleAvailabilityCheck` maintenance/command for every active rule. |

Replace symbol (*) with one of those note types:

- `payment`
- `update_order`
- `update_order_item`
- `email`
- `order_comment`

## Workflow

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.workflow.valid_transitions` | WorkflowTransitionEvent | Valid Event: Use it if you need to extend the workflow transitions |

## Transformer

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.quote_item.pre_transform` | GenericEvent | Fires before proposal item gets transformed to a quote item |
| `coreshop.quote_item.post_transform` | GenericEvent | Fires after proposal item has been transformed to a quote item |
| `coreshop.order_item.pre_transform` | GenericEvent | Fires before proposal item gets transformed to a order item |
| `coreshop.order_item.post_transform` | GenericEvent | Fires after proposal item has been transformed to a order item |
| `coreshop.quote.pre_transform` | GenericEvent | Fires before proposal gets transformed to a quote |
| `coreshop.quote.post_transform` | GenericEvent | Fires after proposal has been transformed to a quote |
| `coreshop.order.pre_transform` | GenericEvent | Fires before proposal gets transformed to a order |
| `coreshop.order.post_transform` | GenericEvent | Fires after proposal has been transformed to a order |
| `coreshop.shipment_item.pre_transform` | GenericEvent | Fires before proposal item gets transformed to a shipment item |
| `coreshop.shipment_item.post_transform` | GenericEvent | Fires after proposal item has been transformed to a shipment item |
| `coreshop.shipment.pre_transform` | GenericEvent | Fires before proposal gets transformed to a shipment |
| `coreshop.shipment.post_transform` | GenericEvent | Fires after proposal has been transformed to a shipment |
| `coreshop.invoice.pre_transform` | GenericEvent | Fires before proposal gets transformed to a invoice |
| `coreshop.invoice.post_transform` | GenericEvent | Fires after proposal has been transformed to a invoice |

## Backend Controller

| Name | EventType | Description |
|:-----|:------------|:----------|
| `coreshop.*.pre_create` | ResourceControllerEvent | Fires before object gets created in backend |
| `coreshop.*.post_create` | ResourceControllerEvent | Fires after object gets created in backend |
| `coreshop.*.pre_save` | ResourceControllerEvent | Fires before object gets saved in backend |
| `coreshop.*.post_save` | ResourceControllerEvent | Fires after object gets saved in backend |
| `coreshop.*.pre_delete` | ResourceControllerEvent | Fires before object gets deleted in backend |
| `coreshop.*.post_delete` | ResourceControllerEvent |Fires after object gets deleted in backend  |

Replace symbol (*) with one of those controller:

- `configuration`
- `payment_provider`
- `exchange_rate`
- `filter`
- `index`
- `notification_rule`
- `notification_rule`
- `cart_price_rule`
- `product_price_rule`
- `shipping_rule`
- `store`
- `tax_rule_group`

## Workflow Events
There are events vor every state machine transition. Read more about it [here](../17_State_Machine/README.md).

## Model Events
You can use Pimcore Events for CoreShops Pimcore Models: [Pimcore Events](https://www.pimcore.org/docs/5.0.0/Extending_Pimcore/Event_API_and_Event_Manager.html)
