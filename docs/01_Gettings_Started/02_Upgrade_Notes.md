# CoreShop Upgrade Notes

Always check this page for some important upgrade notes before updating to the latest coreshop build.

## Update from Version 1.* to 2
CoreShop 2 is **NOT** backward compatible. The reason should be very clear, Symfony provides way more possibilities. And to use all of these, the decision was to not make it backward compatible.

However, most of the models stayed the same, so it should be possible to migrate data from CoreShop 1 to CoreShop 2.

Models that changed:

 - CoreShopCartItem
   - amount is now named quantity
 - CoreShopOrderItem
   - amount is now named quantity
 - CoreShopOrderInvoiceItem
   - amount is now named quantity
 - CoreShopOrderShipmentItem
   - amount is now named quantity
 - CoreShopCategory
  - removed parentCategory field, hierarchy is now solved via Pimcore's Object Tree

There is also a Import/Export Plugin/Bundle to get Data from CoreShop 1 into CoreShop 2:

 - [ImportBundle](https://github.com/coreshop/ImportBundle)
 - [ExportPlugin](https://github.com/coreshop/CoreShopExport)