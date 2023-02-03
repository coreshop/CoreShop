<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ChangePasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\Address\AddressAssignmentManagerInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends FrontendController
{
    public function headerAction(Request $request): Response
    {
        return $this->render($this->templateConfigurator->findTemplate('Customer/_header.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    public function footerAction(): Response
    {
        return $this->render($this->templateConfigurator->findTemplate('Customer/_footer.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    public function profileAction(): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE');

        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/profile.html'), [
            'customer' => $customer,
        ]);
    }

    public function ordersAction(): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ORDERS');

        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/orders.html'), [
            'customer' => $customer,
            'orders' => $this->get('coreshop.repository.order')->findOrdersByCustomer($this->getCustomer()),
        ]);
    }

    public function orderDetailAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ORDER_DETAIL');

        $orderId = $this->getParameterFromRequest($request, 'order');
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $order = $this->get('coreshop.repository.order')->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        if (!$order->getCustomer() instanceof CustomerInterface || $order->getCustomer()->getId() !== $customer->getId()) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/order_detail.html'), [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    public function addressesAction(): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ADDRESSES');

        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/addresses.html'), [
            'customer' => $customer,
        ]);
    }

    public function addressAction(Request $request): Response
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $this->getParameterFromRequest($request, 'address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        if ($address instanceof AddressInterface) {
            $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ADDRESS_EDIT');
        } else {
            $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ADDRESS_ADD');
        }

        $addressAssignmentManager = $this->get(AddressAssignmentManagerInterface::class);

        $eventType = 'update';
        if (!$address instanceof AddressInterface) {
            $eventType = 'add';
            /** @var AddressInterface $address */
            $address = $this->get('coreshop.factory.address')->createNew();
            if ($request->query->has('address_identifier')) {
                $addressIdentifier = $this->get('coreshop.repository.address_identifier')->findByName($request->query->get('address_identifier'));
                if ($addressIdentifier instanceof AddressIdentifierInterface) {
                    $address->setAddressIdentifier($addressIdentifier);
                }
            }
        }

        if ($eventType === 'update' && $addressAssignmentManager->checkAddressAffiliationPermissionForCustomer($customer, $address) === false) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        $addressFormOptions = [
            'available_affiliations' => $addressAssignmentManager->getAddressAffiliationTypesForCustomer($customer),
            'selected_affiliation' => $addressAssignmentManager->detectAddressAffiliationForCustomer($customer, $address),
        ];

        $form = $this->get('form.factory')->createNamed('coreshop', AddressType::class, $address, $addressFormOptions);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            $addressAffiliation = $form->has('addressAffiliation') ? $form->get('addressAffiliation')->getData() : null;

            if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                $address = $handledForm->getData();
                $address->setPublished(true);
                $address->setKey(uniqid());

                $address = $addressAssignmentManager->allocateAddressByAffiliation($customer, $address, $addressAffiliation);

                $this->fireEvent($request, $address, sprintf('%s.%s.%s_post', 'coreshop', 'address', $eventType));
                $this->addFlash('success', $this->get('translator')->trans(sprintf('coreshop.ui.customer.address_successfully_%s', $eventType === 'add' ? 'added' : 'updated')));

                return $this->redirect(
                    $this->getParameterFromRequest($request, '_redirect', $this->generateUrl('coreshop_customer_addresses')),
                );
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/address.html'), [
            'address' => $address,
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function addressDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_ADDRESS_DELETE');

        $customer = $this->getCustomer();
        $addressAssignmentManager = $this->get(AddressAssignmentManagerInterface::class);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $address = $this->get('coreshop.repository.address')->find(
            $this->getParameterFromRequest($request, 'address'),
        );

        if (!$address instanceof AddressInterface) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        if ($addressAssignmentManager->checkAddressAffiliationPermissionForCustomer($customer, $address) === false) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        $this->fireEvent($request, $address, sprintf('%s.%s.%s_pre', 'coreshop', 'address', 'delete'));

        $address->delete();

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.address_successfully_deleted'));

        return $this->redirectToRoute('coreshop_customer_addresses');
    }

    public function settingsAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_SETTINGS');

        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $this->get('form.factory')->createNamed('coreshop', CustomerType::class, $customer, [
            'customer' => $customer->getId(),
            'allow_default_address' => true,
        ]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                $customer = $handledForm->getData();
                $customer->save();

                $this->fireEvent($request, $customer, sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'update'));
                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.profile_successfully_updated'));

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/settings.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function changePasswordAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('CORESHOP_CUSTOMER_PROFILE_CHANGE_PASSWORD');

        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$customer->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $this->get('form.factory')->createNamed('coreshop', ChangePasswordType::class);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                $formData = $handledForm->getData();
                $customer->getUser()->setPassword($formData['password']);
                $customer->getUser()->save();

                $this->fireEvent($request, $customer->getUser(), sprintf('%s.%s.%s_post', 'coreshop', 'user', 'change_password'));
                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.password_successfully_changed'));

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/change_password.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function confirmNewsletterAction(Request $request): Response
    {
        $success = false;
        $token = $this->getParameterFromRequest($request, 'token');
        $newsletterUser = null;

        if (!$token) {
            return $this->redirectToRoute('coreshop_index');
        }

        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->get('coreshop.repository.customer')->findByNewsletterToken($token);

        if ($customer instanceof CustomerInterface) {
            $customer->setNewsletterConfirmed(true);
            $customer->setNewsletterToken(null);

            VersionHelper::useVersioning(function () use ($customer) {
                $customer->save();
            }, false);

            $this->fireEvent($request, $customer, sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'newsletter_confirm'));
            $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.newsletter_confirmed'));
            $success = true;
        } else {
            $this->addFlash('error', $this->get('translator')->trans('coreshop.ui.newsletter_confirmation_error'));
        }

        return $this->render($this->templateConfigurator->findTemplate('Customer/confirm_newsletter.html'), [
            'newsletterUser' => $newsletterUser,
            'success' => $success,
        ]);
    }

    protected function getCustomer(): ?CustomerInterface
    {
        try {
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->get(CustomerContextInterface::class)->getCustomer();

            return $customer;
        } catch (\Exception) {
            // fail silently
        }

        return null;
    }

    protected function fireEvent(Request $request, mixed $object, string $eventName): void
    {
        //@todo: move this to a resource controller event

        $event = new ResourceControllerEvent($object, ['request' => $request]);
        $this->get('event_dispatcher')->dispatch($event, $eventName);
    }
}
