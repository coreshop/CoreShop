<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ChangePasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Bundle\ResourceBundle\Controller\EventDispatcher;
use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Repository\AddressIdentifierRepositoryInterface;
use CoreShop\Component\Core\Context\ShopperContext;
use CoreShop\Component\Core\Customer\Address\AddressAssignmentManagerInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerController extends FrontendController
{
    public function headerAction(
        ShopperContext $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/_header.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer($shopperContext),
        ]);
    }

    public function footerAction(
        ShopperContext $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/_footer.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer($shopperContext),
        ]);
    }

    public function profileAction(
        ShopperContext $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/profile.html'), [
            'customer' => $customer,
        ]);
    }

    public function ordersAction(
        ShopperContext $shopperContext,
        OrderRepositoryInterface $orderRepository,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/orders.html'), [
            'customer' => $customer,
            'orders' => $orderRepository->findByCustomer($customer),
        ]);
    }

    public function orderDetailAction(
        Request $request,
        ShopperContext $shopperContext,
        OrderRepositoryInterface $orderRepository,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $orderId = $request->get('order');
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $order = $orderRepository->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        if (!$order->getCustomer() instanceof CustomerInterface || $order->getCustomer()->getId() !== $customer->getId()) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/order_detail.html'), [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    public function addressesAction(
        ShopperContext $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/addresses.html'), [
            'customer' => $customer,
        ]);
    }

    public function addressAction(
        Request $request,
        ShopperContext $shopperContext,
        RepositoryInterface $addressRepository,
        FactoryInterface $addressFactory,
        AddressIdentifierRepositoryInterface $addressIdentifierRepository,
        AddressAssignmentManagerInterface $addressAssignmentManager,
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $request->get('address');
        $address = $addressRepository->find($addressId);

        $eventType = 'update';
        if (!$address instanceof AddressInterface) {
            $eventType = 'add';
            /** @var AddressInterface $address */
            $address = $addressFactory->createNew();
            if ($request->query->has('address_identifier')) {
                $addressIdentifier = $addressIdentifierRepository->findByName($request->query->get('address_identifier'));
                if ($addressIdentifier instanceof AddressIdentifierInterface) {
                    $address->setAddressIdentifier($addressIdentifier);
                }
            }
        }

        if ($eventType === 'update' && $addressAssignmentManager->checkAddressAffiliationPermissionForCustomer($customer,
                $address) === false) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        $addressFormOptions = [
            'available_affiliations' => $addressAssignmentManager->getAddressAffiliationTypesForCustomer($customer),
            'selected_affiliation' => $addressAssignmentManager->detectAddressAffiliationForCustomer($customer, $address)
        ];

        $form = $formFactory->createNamed('address', AddressType::class, $address, $addressFormOptions);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            $addressAffiliation = $form->has('addressAffiliation') ? $form->get('addressAffiliation')->getData() : null;

            if ($handledForm->isValid()) {

                $address = $handledForm->getData();
                $address->setPublished(true);
                $address->setKey(uniqid());

                $address = $addressAssignmentManager->allocateAddressByAffiliation($customer, $address,
                    $addressAffiliation);

                $this->fireEvent(
                    $request,
                    $eventDispatcher,
                    $address,
                    sprintf('%s.%s.%s_post', 'coreshop', 'address', $eventType)
                );
                $this->addFlash('success',
                    $translator->trans(
                        sprintf('coreshop.ui.customer.address_successfully_%s',
                        $eventType === 'add' ? 'added' : 'updated')
                    )
                );

                return $this->redirect(
                    $request->get('_redirect',
                    $this->generateCoreShopUrl($customer, 'coreshop_customer_addresses'))
                );
            }
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/address.html'), [
            'address' => $address,
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function addressDeleteAction(
        Request $request,
        ShopperContext $shopperContext,
        RepositoryInterface $addressRepository,
        AddressAssignmentManagerInterface $addressAssignmentManager,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $address = $addressRepository->find($request->get('address'));

        if (!$address instanceof AddressInterface) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        if ($addressAssignmentManager->checkAddressAffiliationPermissionForCustomer($customer, $address) === false) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        }

        $this->fireEvent(
            $request,
            $eventDispatcher,
            $address,
            sprintf('%s.%s.%s_pre', 'coreshop', 'address', 'delete')
        );

        $address->delete();

        $this->addFlash('success', $translator->trans('coreshop.ui.customer.address_successfully_deleted'));

        return $this->redirectToRoute('coreshop_customer_addresses');
    }

    public function settingsAction(
        Request $request,
        ShopperContext $shopperContext,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $formFactory->createNamed('', CustomerType::class, $customer, [
            'customer' => $customer->getId(),
            'allow_default_address' => true,
        ]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $customer = $handledForm->getData();
                $customer->save();

                $this->fireEvent(
                    $request,
                    $eventDispatcher,
                    $customer,
                    sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'update')
                );
                $this->addFlash(
                    'success',
                    $translator->trans('coreshop.ui.customer.profile_successfully_updated')
                );

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/settings.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function changePasswordAction(
        Request $request,
        ShopperContext $shopperContext,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $customer = $this->getCustomer($shopperContext);

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $formFactory->createNamed('', ChangePasswordType::class);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $formData = $handledForm->getData();
                $customer->setPassword($formData['password']);
                $customer->save();

                $this->fireEvent(
                    $request,
                    $eventDispatcher,
                    $customer,
                    sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'change_password')
                );
                $this->addFlash(
                    'success',
                    $translator->trans('coreshop.ui.customer.password_successfully_changed'));

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/change_password.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    public function confirmNewsletterAction(
        Request $request,
        CustomerRepositoryInterface $customerRepository,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $success = false;
        $token = $request->get('token');
        $newsletterUser = null;

        if (!$token) {
            return $this->redirectToRoute('coreshop_index');
        }

        /**
         * @var CustomerInterface $customer
         */
        $customer = $customerRepository->findByNewsletterToken($token);

        if ($customer instanceof \CoreShop\Component\Core\Model\CustomerInterface) {
            $customer->setNewsletterConfirmed(true);
            $customer->setNewsletterToken(null);

            VersionHelper::useVersioning(function () use ($customer) {
                $customer->save();
            }, false);

            $this->fireEvent(
                $request,
                $eventDispatcher,
                $customer,
                sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'newsletter_confirm')
            );

            $this->addFlash('success', $translator->trans('coreshop.ui.newsletter_confirmed'));
            $success = true;
        } else {
            $this->addFlash('error', $translator->trans('coreshop.ui.newsletter_confirmation_error'));
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Customer/confirm_newsletter.html'), [
            'newsletterUser' => $newsletterUser,
            'success' => $success,
        ]);
    }

    protected function getCustomer(ShopperContext $shopperContext): ?CustomerInterface
    {
        if ($shopperContext->hasCustomer()) {
            return $shopperContext->getCustomer();
        }

        return null;
    }

    protected function fireEvent(Request $request, EventDispatcherInterface $eventDispatcher, $object, string $eventName)
    {
        $event = new ResourceControllerEvent($object, ['request' => $request]);
        $eventDispatcher->dispatch($eventName, $event);
    }
}
