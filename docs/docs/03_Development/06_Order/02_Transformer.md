# CoreShop Transfomers

Transfomers, as the name say, transform Data from different formats. A transformer converts between Object Types. For example: Cart -> Order. Following implementation do exist right now:

 - [an Order into an Invoice](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/OrderToInvoiceTransformer.php)
 - [an Order into an Shipment](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/OrderToShipmentTransformer.php)


The base transformer interface for all "Proposals" is ```CoreShop\Component\Order\Transformer\ProposalTransformerInterface```

## Extending Transfomers

If you ever have the need to adapt the transfomer, eg. hook into it and store some custom data into the order, you can do so
by decorating the default service. Following Services are used by CoreShop for all different Transfomers:

| From                 | To                 |  Service                                                         |
|----------------------|--------------------|------------------------------------------------------------------|
| Order                | Invoice            | coreshop.order.transformer.order_to_invoice                      |
| OrderItem            | InvoiceItem        | coreshop.order_invoice.transformer.cart_item_to_order_item       |
| Order                | Shipment           | coreshop.order.transformer.order_to_invoice                      |
| OrderItem            | ShipmentItem       | coreshop.order_invoice.transformer.order_item_to_shipment_item   |
