# Extending CoreShop

CoreShop Data Objects (like CoreShopProduct or CoreShopOrder) can be changed directly within Pimcore.

## Replace CoreShop Object Classes with your own Classes

CoreShop uses Pimcore Parameters to determine the Pimcore Object Class. To change it, simply add this to your config.yml

```yaml
core_shop_order:
    pimcore:
        order:
            classes:
                model: 'Pimcore\Model\DataObject\MyOrderClass'
                install_file: '@AppBundle/Resources/install/pimcore/classes/MyOrderClass.json'
                repository: AppBundle\Repository\OrderRepository
                factory: AppBundle\Factory\OrderFactory
```

