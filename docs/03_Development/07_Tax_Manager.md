# CoreShop Carrier

CoreShop implements a Taxes using Tax Rules. If you need something different for tax calculation, you can need to implement the class CoreShop\Bundle\LegacyBundle\Model\Plugin\TaxManager.

To notify CoreShop that a custom TaxManager is available, you need to hook into "tax.getTaxManager".

You can find a example implementation [here](https://github.com/coreshop/coreshop-demo-taxmanager)