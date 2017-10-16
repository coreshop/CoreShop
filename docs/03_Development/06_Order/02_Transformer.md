# CoreShop Transfomers

Transfomers, as the name say, transform Data from different formats. A transformer converts between Object Types. For example: Cart -> Order. Following implementation do exist right now:

 - [an Cart into an Order](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/CartToOrderTransformer.php)
 - [an Cart into an Quote](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/CartToQuoteTransformer.php)
 - [an Order into an Invoice](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/OrderToInvoiceTransformer.php)
 - [an Order into an Shipment](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/OrderBundle/Transformer/OrderToShipmentTransformer.php)


The base transformer interface for all "Proposals" is ```CoreShop\Component\Order\Transformer\ProposalTransformerInterface```

## Extending Transfomers

If you ever have the need to adapt the transfomer, eg. hook into it and store some custom data into the order, you can do so
by decorating the default service. Following Services are used by CoreShop for all different Transfomers:

| From                 | To                 |  Service                                                         |
|----------------------|--------------------|------------------------------------------------------------------|
| Cart                 | Order              | coreshop.order.transformer.cart_to_order                         |
| CartItem             | OrderItem          | coreshop.order.transformer.cart_item_to_order_item               |
| Cart                 | Quote              | coreshop.order.transformer.cart_to_quote                         |
| CartItem             | QuoteItem          | coreshop.order.transformer.cart_item_to_quote_item               |
| Order                | Invoice            | coreshop.order.transformer.order_to_invoice                      |
| OrderItem            | InvoiceItem        | coreshop.order_invoice.transformer.cart_item_to_order_item       |
| Order                | Shipment           | coreshop.order.transformer.order_to_invoice                      |
| OrderItem            | ShipmentItem       | coreshop.order_invoice.transformer.order_item_to_shipment_item   |

## Example of extending

```php
<?php

namespace AppBundle\CoreShop\Order\Transformer;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use Webmozart\Assert\Assert;

final class CustomOrderTransfomer implements ProposalTransformerInterface
{
     /**
     * @var ProposalTransformerInterface
     */
    protected $innerCartToOrderTransformer;

    public function __construct(ProposalTransformerInterface $innerCartToOrderTransformer)
    {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
    }

    public function transform(ProposalInterface $cart, ProposalInterface $sale)
    {
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);

        $sale = $this->innerCartToOrderTransformer->transform($cart, $sale);

        $cart->setCustomField($sale->getCustomField() * 100);
    }
}
```

Now we need to register our class the container set the decorator:

```yaml
app.coreshop.order.transformer.cart_to_order:
    decorates: coreshop.order.transformer.cart_to_order
    class: AppBundle\CoreShop\Order\Transformer\CustomOrderTransformer
    arguments:
      - '@app.coreshop.order.transformer.cart_to_order.inner'
```

Everytime CoreShop now calls the Order Transformer, your CustomOrderTransfomer gets called as well.