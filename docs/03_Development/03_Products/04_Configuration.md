# Product Configuration

```yaml
core_shop_product:
    pimcore:
        product:
            path: coreshop/products
            classes:
                repository: CoreShop\Bundle\CoreBundle\Pimcore\Repository\ProductRepository
                install_file: '@CoreShopCoreBundle/Resources/install/pimcore/classes/CoreShopProductBundle/CoreShopProduct.json'
                model: Pimcore\Model\DataObject\CoreShopProduct
                interface: CoreShop\Component\Product\Model\ProductInterface
                factory: CoreShop\Component\Resource\Factory\PimcoreFactory
                type: object
```