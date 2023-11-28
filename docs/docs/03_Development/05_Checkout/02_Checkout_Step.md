# Checkout Step

If you want to implement a custom checkout step, you need to implement the
interface ```CoreShop\Component\Order\Checkout\CheckoutStepInterface```
and register your step into your Cart Manager:

```yaml
# app/config/config.yml
core_shop_core:
    checkout:
        default:
            steps:
              custom:
                  step: app.coreshop.checkout.custom
                  priority: 50
```

The [Checkout Controller](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/FrontendBundle/Controller/CheckoutController.php#L44)
takes care about handling
the Checkout for you then.

## Optional Checkout Step

If you have an optional checkout step - depending on the cart, your Checkout Step can implement the
interface ```CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface```.

You need to implement the function ```isRequired(OrderInterface $cart)```

### Optional Checkout Step Example

```php
<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
    private FormFactoryInterface $formFactory;
    private StoreContextInterface $storeContext;
    private CartManagerInterface $cartManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        StoreContextInterface $storeContext,
        CartManagerInterface $cartManager
    )
    {
        $this->formFactory = $formFactory;
        $this->storeContext = $storeContext;
        $this->cartManager = $cartManager;
    }

    public function getIdentifier(): string
    {
        return 'payment';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return $cart->getTotal() > 0;
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function validate(OrderInterface $cart): bool
    {
        return $cart->hasItems() && $cart->getPaymentProvider() instanceof PaymentProviderInterface;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

                return true;
            } else {
                throw new CheckoutException('Payment Form is invalid', 'coreshop.ui.error.coreshop_checkout_payment_form_invalid');
            }
        }

        return false;
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        return [
            'form' => $this->createForm($request, $cart)->createView(),
        ];
    }

    private function createForm(Request $request, OrderInterface $cart)
    {
        $form = $this->formFactory->createNamed('', PaymentType::class, $cart, [
            'payment_subject' => $cart
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

```
