<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ChangePasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerAction(Request $request)
    {
        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/_header.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function footerAction()
    {
        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/_footer.html'), [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function profileAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/profile.html'), [
            'customer' => $customer,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ordersAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/orders.html'), [
            'customer' => $customer,
            'orders' => $this->get('coreshop.repository.order')->findByCustomer($this->getCustomer()),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function orderDetailAction(Request $request)
    {
        $orderId = $request->get('order');
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

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/order_detail.html'), [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addressesAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/addresses.html'), [
            'customer' => $customer,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addressAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        $eventType = 'update';
        if (!$address instanceof AddressInterface) {
            $eventType = 'add';
            /** @var AddressInterface $address */
            $address = $this->get('coreshop.factory.address')->createNew();
            if ($request->query->has('address_identifier')) {
                $addressIdentifier = $this->get('coreshop.repository.address_identifier')->findOneBy(['name' => $request->query->get('address_identifier')]);
                if ($addressIdentifier instanceof AddressIdentifierInterface) {
                    $address->setAddressIdentifier($addressIdentifier);
                }
            }
        } else {
            if (!$customer->hasAddress($address)) {
                return $this->redirectToRoute('coreshop_customer_addresses');
            }
        }

        $form = $this->get('form.factory')->createNamed('address', AddressType::class, $address);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $address = $handledForm->getData();

                $address->setPublished(true);
                $address->setKey(uniqid());
                $address->setParent($this->get('coreshop.object_service')->createFolderByPath(sprintf('/%s/%s', $customer->getFullPath(), $this->getParameter('coreshop.folder.address'))));
                $address->save();

                // todo: move this to a resource controller event
                $event = new ResourceControllerEvent($address, ['request' => $request]);
                $this->get('event_dispatcher')->dispatch(
                    sprintf('%s.%s.%s_post', 'coreshop', 'address', $eventType),
                    $event
                );

                $customer->addAddress($address);
                $customer->save();

                $this->addFlash('success', $this->get('translator')->trans(sprintf('coreshop.ui.customer.address_successfully_%s', $eventType === 'add' ? 'added' : 'updated')));

                return $this->redirect($request->get('_redirect', $this->generateCoreShopUrl($customer, 'coreshop_customer_addresses')));
            }
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/address.html'), [
            'address' => $address,
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addressDeleteAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        if (!$address instanceof AddressInterface) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        } else {
            if (!$customer->hasAddress($address)) {
                return $this->redirectToRoute('coreshop_customer_addresses');
            }
        }

        // todo: move this to a resource controller event
        $event = new ResourceControllerEvent($address, ['request' => $request]);
        $this->get('event_dispatcher')->dispatch(
            sprintf('%s.%s.%s_pre', 'coreshop', 'address', 'delete'),
            $event
        );

        $address->delete();

        $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.address_successfully_deleted'));

        return $this->redirectToRoute('coreshop_customer_addresses');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $this->get('form.factory')->createNamed('', CustomerType::class, $customer, [
            'customer' => $customer->getId(),
            'allow_default_address' => true,
        ]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $customer = $handledForm->getData();
                $customer->save();

                // todo: move this to a resource controller event
                $event = new ResourceControllerEvent($customer, ['request' => $request]);
                $this->get('event_dispatcher')->dispatch(
                    sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'update'),
                    $event
                );

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.profile_successfully_updated'));

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/settings.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $this->get('form.factory')->createNamed('', ChangePasswordType::class);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $formData = $handledForm->getData();
                $customer->setPassword($formData['password']);
                $customer->save();

                // todo: move this to a resource controller event
                $event = new ResourceControllerEvent($customer, ['request' => $request]);
                $this->get('event_dispatcher')->dispatch(
                    sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'change_password'),
                    $event
                );

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.customer.password_successfully_changed'));

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/change_password.html'), [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmNewsletterAction(Request $request)
    {
        $success = false;
        $token = $request->get('token');
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

            $event = new ResourceControllerEvent($customer, ['request' => $request]);
            $this->get('event_dispatcher')->dispatch(
                sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'newsletter_confirm'),
                $event
            );

            $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.newsletter_confirmed'));
            $success = true;
        } else {
            $this->addFlash('error', $this->get('translator')->trans('coreshop.ui.newsletter_confirmation_error'));
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Customer/confirm_newsletter.html'), [
            'newsletterUser' => $newsletterUser,
            'success' => $success,
        ]);
    }

    /**
     * @return CustomerInterface|null
     */
    protected function getCustomer()
    {
        try {
            return $this->get('coreshop.context.customer')->getCustomer();
        } catch (\Exception $ex) {
        }

        return null;
    }
}
