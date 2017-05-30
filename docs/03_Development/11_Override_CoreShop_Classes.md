# Extending CoreShop

CoreShop-Objects (like CoreShopProduct or CoreShopOrder) should not be changed directly within Pimcore.

CoreShop uses Pimcore Parameters to determine the Pimcore Object Class. To change it, simply add this to your services.yml

```
parameters:
    coreshop:
        model:
            order:
                class: 'Pimcore\Model\Object\MyOrderClass'
                install_file: '@AcmeBundle/Resources/install/pimcore/classes/MyOrderClass.json'
                repository: AcmeBundle\Repository\OrderRepository
                admin_controller: AcmeBundle\Controller\OrderController
                factory: AcmeBundle\Factory\OrderFactory

```