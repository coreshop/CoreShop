# CoreShop Events

CoreShop Resource Bundle calls event for each resource:

coreshop.country.pre_save
coreshop.country.post_save
coreshop.state.pre_save
coreshop.state.post_save

CoreShop also calls Event for Order Operations:

coreshop.order.pre_transform
coreshop.order.post_transform
coreshop.order_item.pre_transform
coreshop.order_item.post_transform

You can use Pimcore Events for CoreShop' s Pimcore Models: [Pimcore Events](https://www.pimcore.org/docs/5.0.0/Extending_Pimcore/Event_API_and_Event_Manager.html)