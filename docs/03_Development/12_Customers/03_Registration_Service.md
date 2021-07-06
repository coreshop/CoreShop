# CoreShop Customer Registration Service
CoreShop already implements a registration Service which handles creating a Customer with Addresses. 
The Registration Service implements the Interface `CoreShop\Bundle\CoreBundle\Customer` and CoreShop implements it using the service `coreshop.customer.registration_service`.

## Usage
To use the Service, you need to pass a Customer, Address, additional Formdata and if Registration is Guest or Customer.
In our example, we gonna do that from a Controller with a FormType.

```php
$customer = $this->getCustomer();

if ($customer instanceof CustomerInterface) {
    return $this->redirectToRoute('coreshop_customer_profile');
}

$form = $this->get('form.factory')->createNamed('', CustomerRegistrationType::class);

if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
    $handledForm = $form->handleRequest($request);

    if ($handledForm->isValid()) {
        $formData = $handledForm->getData();

        $customer = $formData['customer'];
        $address = $formData['address'];

        if (!$customer instanceof \CoreShop\Component\Core\Model\CustomerInterface ||
            !$address instanceof AddressInterface
        ) {
            return $this->renderTemplate('CoreShopFrontendBundle:Register:register.html.twig', [
                'form' => $form->createView()
            ]);
        }

        $registrationService = $this->get('coreshop.customer.registration_service');

        try {
            $registrationService->registerCustomer($customer, $address, $formData, false);
        } catch (CustomerAlreadyExistsException $e) {
            return $this->renderTemplate('CoreShopFrontendBundle:Register:register.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('coreshop_customer_profile');
    }

    return $this->renderTemplate('CoreShopFrontendBundle:Register:register.html.twig', [
        'form' => $form->createView()
    ]);
}
```