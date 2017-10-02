# CoreShop Payment

CoreShop contains a very flexible payments management system with support for many gateways (payment providers). We are using a payment abstraction library - [Payum](https://github.com/payum/payum), which handles all sorts of capturing, refunding and recurring payments logic.

On CoreShop side, we integrate it into our checkout and manage all the payment data.

## Payment
Every payment in CoreShop, successful or failed, is represented by the Payment model, which contains basic information and a reference to appropriate order.

## Create a Payment programmatically

As usually, use a factory to create a new PaymentMethod and give it a unique code.

```php
$payment = $this->container->get('coreshop.factory.payment')->createNew();

$payment->setOrder($order);
$payment->setCurrencyCode('EUR');

$this->container->get('coreshop.repository.payment')->add($payment);
```

## More

 - [Payment Provider](./01_Payment_Provider.md)
 - [Ominpay Bridge](./02_Omnipay_Bridge.md)
 - [Payum Providers](./03_Payum_Providers.md)