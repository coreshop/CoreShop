# Extending CoreShop

CoreShop Data Objects (like CoreShopProduct or CoreShopOrder) should not be changed directly within Pimcore.

CoreShop uses Pimcore Parameters to determine the Pimcore Object Class. To change it, simply add this to your config.yml

```yaml
core_shop_order:
    pimcore:
        order:
            classes:
                class: 'Pimcore\Model\Object\MyOrderClass'
                install_file: '@AppBundle/Resources/install/pimcore/classes/MyOrderClass.json'
                repository: AppBundle\Repository\OrderRepository
                factory: AppBundle\Factory\OrderFactory
```

TODO: Be more clear about that
