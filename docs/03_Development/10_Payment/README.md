# CoreShop Payment

CoreShop contains a very flexible payments management system with support for many gateways (payment providers).
We are using a payment abstraction library - [Payum](https://github.com/payum/payum),
which handles all sorts of capturing, refunding and recurring payments logic.

On CoreShop side, we integrate it into our checkout and manage all the payment data.

## Available Payment Gateways for CoreShop

| Name | State | Link | Support | Offsite | Supports Server-Notification | Supports Refund |
|------|------|-------|---------|---------|------------------------------|-----------------|
| **Heidelpay** | stable | [Github](https://github.com/coreshop/PayumHeidelpayBundle) | PayPal, Klarna Sofort, Credit Card | No | Yes | No |
| **SaferPay** | stable | [Github](https://github.com/coreshop/PayumSaferpayBundle) | [Supported Payment Methods](https://saferpay.github.io/sndbx/index.html#paymentmethods) | Yes | Yes | Partially |
| **PostFinance** | stable | [Github](https://github.com/coreshop/PayumPostFinanceBundle) | PostFinance Card, PostFinance E-Finance, Visa, MasterCard, Diners Club, American Express, JCB, PayPal, TWINT | Yes | Yes | No |
| **PowerPay** | dev | [Github](https://github.com/coreshop/PayumPowerpayBundle) | invoice, automatic credit check | No | No (not required) | No |
| **CuraBill** | dev | [Github](https://github.com/coreshop/PayumCurabillBundle) | invoice, instalments via iframe | No | No (not required) | No |

## Payment
Every payment in CoreShop, successful or failed, is represented by the payment model,
which contains basic information and a reference to appropriate order.

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