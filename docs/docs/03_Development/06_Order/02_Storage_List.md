# Storage List

In CoreShop, the functionalities of Order, Cart, Quote, and Wishlist are unified, thanks to the abstraction into a
dedicated Bundle.

## Configuration

The default configuration for the Order StorageList is as follows:

```yaml
core_shop_storage_list:
    list:
        order:
            context:
                interface: CoreShop\Component\Order\Context\CartContextInterface
                composite: CoreShop\Component\Order\Context\CompositeCartContext
                tag: coreshop.context.cart
                restore_customer_list_only_on_login: false
            services:
                manager: CoreShop\Bundle\OrderBundle\Manager\CartManager
                modifier: CoreShop\Component\Order\Cart\CartModifier
                enable_default_store_based_decorator: false
            session:
                enabled: true
                key: coreshop.cart
            form:
                type: CoreShop\Bundle\OrderBundle\Form\Type\CartType
                add_type: CoreShop\Bundle\OrderBundle\Form\Type\AddToCartType
            resource:
                interface: CoreShop\Component\Core\Model\OrderInterface
                product_repository: coreshop.repository.stack.purchasable
                repository: coreshop.repository.order
                item_repository: coreshop.repository.order_item
                factory: coreshop.factory.order
                item_factory: coreshop.factory.order_item
                add_to_list_factory: CoreShop\Bundle\OrderBundle\Factory\AddToCartFactoryInterface
            routes:
                summary: coreshop_cart_summary
                index: coreshop_index
            templates:
                add_to_cart: '@CoreShopFrontend/Product/_addToCart.html.twig'
                summary: '@CoreShopFrontend/Cart/summary.html.twig'
            controller:
                enabled: false
                class: CoreShop\Bundle\StorageListBundle\Controller\StorageListController
            expiration:
                enabled: true
                service: CoreShop\Bundle\OrderBundle\Expiration\OrderAndCartExpiration
                days: 0
                params:
                    cart:
                        days: 0
                        params:
                            anonymous: true
                            customer: false
                    order:
                        days: 20
```
