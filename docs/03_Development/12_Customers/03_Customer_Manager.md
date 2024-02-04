# Customer Manager

CoreShop provides a robust Customer Manager that simplifies the process of creating customers along with their
addresses. This feature is an integral part of the CoreShop suite, ensuring streamlined customer data management.

## CoreShop's Implementation of Customer Manager

The Customer Manager in CoreShop adheres to the `CoreShop\Bundle\CoreBundle\Customer\CustomerManagerInterface`
interface. CoreShop implements this interface through the service `coreshop.customer.manager`.

## Utilizing the Customer Manager

The Customer Manager service can be used in various contexts, such as within a controller paired with a form type.
Here’s an example of its usage:

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

In this example, the Customer Manager service is used to handle customer data obtained from a form. The customer is
created or updated based on form submissions, demonstrating the flexibility and ease of use provided by CoreShop's
customer management system.
