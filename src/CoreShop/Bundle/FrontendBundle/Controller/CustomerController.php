<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ChangePasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Pimcore\VersionHelper;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends FrontendController
{
    /**
     * @param Request $request
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

        $view = View::create($customer)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/profile.html'))
            ->setTemplateData([
                'customer' => $customer
            ]);

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ordersAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $view = View::create($customer)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/orders.html'))
            ->setTemplateData([
                'customer' => $customer,
                'orders' => $this->get('coreshop.repository.order')->findByCustomer($this->getCustomer())
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function orderDetailAction(Request $request)
    {
        $orderId = $request->get('order');
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $order = $this->get('coreshop.repository.order')->find($orderId);

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException();
        }

        if (!$order->getCustomer() instanceof CustomerInterface || $order->getCustomer()->getId() !== $customer->getId()) {
            throw new AccessDeniedHttpException();
        }

        $view = View::create($order)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/order_detail.html'))
            ->setTemplateData([
                'order' => $order,
                'customer' => $customer
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addressesAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $view = View::create($customer)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/addresses.html'))
            ->setTemplateData([
                'customer' => $customer,
                'addresses' => $customer->getAddresses()
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addressAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        $eventType = 'update';
        if (!$address instanceof AddressInterface) {
            $eventType = 'add';
            $address = $this->get('coreshop.factory.address')->createNew();
        } else {
            if (!$customer->hasAddress($address)) {
                return $this->viewHandler->handle(View::createRouteRedirect('coreshop_customer_addresses'));
            }
        }

        $form = $this->get('form.factory')->createNamed('address', AddressType::class, $address);

        $redirect = $request->get('_redirect', $this->generateUrl('coreshop_customer_addresses'));
        $form->get('_redirect')->setData($redirect);

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

                $this->addFlash('success', sprintf('coreshop.ui.customer.address_successfully_%s', $eventType === 'add' ? 'added' : 'updated'));

                if ($request->isXmlHttpRequest()) {
                    return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
                }

                return $this->viewHandler->handle(View::createRedirect($handledForm->get('_redirect')->getData()));
            }
        }

        $view = View::create($form)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/address.html'))
            ->setTemplateData([
                'address' => $address,
                'customer' => $customer,
                'form' => $form->createView()
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addressDeleteAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        if (!$address instanceof AddressInterface) {
            throw new NotFoundHttpException();
        } else {
            if (!$customer->hasAddress($address)) {
                throw new AccessDeniedHttpException();
            }
        }

        // todo: move this to a resource controller event
        $event = new ResourceControllerEvent($address, ['request' => $request]);
        $this->get('event_dispatcher')->dispatch(
            sprintf('%s.%s.%s_pre', 'coreshop', 'address', 'delete'),
            $event
        );

        $address->delete();

        $this->addFlash('success', 'coreshop.ui.customer.address_successfully_deleted');

        if ($request->isXmlHttpRequest()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        }

        return $this->viewHandler->handle(View::createRouteRedirect('coreshop_customer_addresses'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->get('form.factory')->createNamed('', CustomerType::class, $customer, [
            'customer' => $customer->getId(),
            'allow_default_address' => true
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

                $this->addFlash('success', 'coreshop.ui.customer.profile_successfully_updated');

                if ($request->isXmlHttpRequest()) {
                    return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
                }

                return $this->viewHandler->handle(View::createRouteRedirect('coreshop_customer_profile'));
            }
        }

        $view = View::create($form)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/settings.html'))
            ->setTemplateData([
                'customer' => $customer,
                'form' => $form->createView()
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new AccessDeniedHttpException();
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

                $this->addFlash('success', 'coreshop.ui.customer.password_successfully_changed');

                if ($request->isXmlHttpRequest()) {
                    return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
                }

                return $this->viewHandler->handle(View::createRouteRedirect('coreshop_customer_profile'));
            }
        }

        $view = View::create($form)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/change_password.html'))
            ->setTemplateData([
                'customer' => $customer,
                'form' => $form->createView()
            ]);

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmNewsletterAction(Request $request)
    {
        $token = $request->get('token');
        $newsletterUser = null;

        if (!$token) {
            throw new AccessDeniedHttpException();
        }

        /**
         * @var $customer CustomerInterface
         */
        $customer = $this->get('coreshop.repository.customer')->findByNewsletterToken($token);

        if ($success = $customer instanceof CustomerInterface) {
            $customer->setNewsletterConfirmed(true);
            $customer->setNewsletterToken(null);

            VersionHelper::useVersioning(function() use ($customer) {
                $customer->save();
            }, false);

            $event = new ResourceControllerEvent($customer, ['request' => $request]);
            $this->get('event_dispatcher')->dispatch(
                sprintf('%s.%s.%s_post', 'coreshop', 'customer', 'newsletter_confirm'),
                $event
            );

            $this->addFlash('success', 'coreshop.ui.newsletter_confirmed');
        } else {
            $this->addFlash('error', 'coreshop.ui.newsletter_confirmation_error');
        }

        $view = View::create($customer)
            ->setTemplate($this->templateConfigurator->findTemplate('Customer/confirm_newsletter.html'))
            ->setTemplateData([
                'newsletterUser' => $newsletterUser,
                'success' => $success
            ]);

        return $this->viewHandler->handle($view);
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
