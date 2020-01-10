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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CoreBundle\Customer\CustomerAlreadyExistsException;
use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Bundle\CustomerBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Bundle\CustomerBundle\Form\Type\RequestResetPasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ResetPasswordType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $customer = $this->getCustomer();

        if ($customer instanceof CustomerInterface && $customer->getIsGuest() === false) {
            return $this->redirectToRoute('coreshop_customer_profile');
        }

        $form = $this->get('form.factory')->createNamed('', CustomerRegistrationType::class);

        $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_customer_profile'));

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $formData = $handledForm->getData();

                $customer = $formData['customer'];
                $address = $formData['address'];

                if (!$customer instanceof \CoreShop\Component\Core\Model\CustomerInterface ||
                    !$address instanceof AddressInterface
                ) {
                    return $this->renderTemplate($this->templateConfigurator->findTemplate('Register/register.html'), [
                        'form' => $form->createView(),
                    ]);
                }

                $registrationService = $this->get('coreshop.customer.registration_service');

                try {
                    $registrationService->registerCustomer($customer, $address, $formData, false);
                } catch (CustomerAlreadyExistsException $e) {
                    return $this->renderTemplate($this->templateConfigurator->findTemplate('Register/register.html'), [
                        'form' => $form->createView(),
                    ]);
                }

                return $this->redirect($redirect);
            }
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Register/register.html'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function passwordResetRequestAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', RequestResetPasswordType::class);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $passwordReset = $handledForm->getData();

                $customer = $this->get('coreshop.repository.customer')->findCustomerByEmail($passwordReset['email']);

                if (!$customer instanceof CustomerInterface) {
                    return $this->redirectToRoute('coreshop_index');
                }

                $customer->setPasswordResetHash(hash('md5', $customer->getId() . $customer->getEmail() . mt_rand() . time()));
                $customer->save();

                $resetLink = $this->generateCoreShopUrl(null, 'coreshop_customer_password_reset', ['token' => $customer->getPasswordResetHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('coreshop.customer.request_password_reset', new RequestPasswordChangeEvent($customer, $resetLink));

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_request_success'));

                return $this->redirectToRoute('coreshop_login');
            }
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Register/password-reset-request.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function passwordResetAction(Request $request)
    {
        $resetToken = $request->get('token');

        if ($resetToken) {
            $customer = $this->get('coreshop.repository.customer')->findByResetToken($resetToken);

            $form = $this->get('form.factory')->createNamed('', ResetPasswordType::class);

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
                $handledForm = $form->handleRequest($request);

                if ($handledForm->isValid()) {
                    $resetPassword = $handledForm->getData();

                    $customer->setPassword($resetPassword['password']);
                    $customer->save();

                    $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_success'));

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('coreshop.customer.password_reset', new GenericEvent($customer));

                    return $this->redirectToRoute('coreshop_login');
                }
            }

            return $this->renderTemplate($this->templateConfigurator->findTemplate('Register/password-reset.html'), [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    /**
     * @return bool|CustomerInterface|null
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
