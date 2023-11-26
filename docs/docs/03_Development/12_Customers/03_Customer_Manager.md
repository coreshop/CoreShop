# Customer Manager

CoreShop already implements a Customer Manager which handles creating a Customer with Addresses.
The Customer Manager implements the Interface `CoreShop\Bundle\CoreBundle\Customer\CustomerManagerInterface` and
CoreShop implements it using the service `coreshop.customer.manager`.

## Usage

To use the Service, you need to pass a Customer.
In our example, we gonna do that from a Controller with a FormType.

```php
$customer = $this->getCustomer();

if ($customer instanceof CustomerInterface && null === $customer->getUser()) {
    return $this->redirectToRoute('coreshop_customer_profile');
}

$form = $this->get('form.factory')->createNamed('customer', CustomerRegistrationType::class, $this->get('coreshop.factory.customer')->createNew());

$redirect = $this->getParameterFromRequest($request, '_redirect', $this->generateUrl('coreshop_customer_profile'));

if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
    $form = $form->handleRequest($request);

    if ($form->isValid()) {
        $customer = $form->getData();
        $customer->setLocaleCode($this->get('coreshop.context.locale')->getLocaleCode());

        $this->get('coreshop.customer.manager')->persistCustomer($customer);

        return $this->redirect($redirect);
    }
}

return $this->render($this->templateConfigurator->findTemplate('Register/register.html'), [
    'form' => $form->createView(),
]);
```