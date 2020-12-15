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

use CoreShop\Bundle\CoreBundle\Customer\CustomerAlreadyExistsException;
use CoreShop\Bundle\CoreBundle\Customer\RegistrationServiceInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Bundle\CustomerBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Bundle\CustomerBundle\Form\Type\RequestResetPasswordType;
use CoreShop\Bundle\CustomerBundle\Form\Type\ResetPasswordType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
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
                    return $this->render($this->templateConfigurator->findTemplate('Register/register.html'), [
                        'form' => $form->createView(),
                    ]);
                }

                $registrationService = $this->get(RegistrationServiceInterface::class);

                try {
                    $registrationService->registerCustomer($customer, $address, $formData, false);
                } catch (CustomerAlreadyExistsException $e) {
                    return $this->render($this->templateConfigurator->findTemplate('Register/register.html'), [
                        'form' => $form->createView(),
                    ]);
                }

                return $this->redirect($redirect);
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Register/register.html'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function passwordResetRequestAction(Request $request)
    {
        $resetIdentifier = $this->container->getParameter('coreshop.customer.security.login_identifier');
        $form = $this->get('form.factory')->createNamed('', RequestResetPasswordType::class, null, ['reset_identifier' => $resetIdentifier]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $passwordResetData = $handledForm->getData();

                $customer = $this->getCustomerRepository()->findUniqueByLoginIdentifier($resetIdentifier, $passwordResetData[$resetIdentifier], false);

                if (!$customer instanceof CustomerInterface) {
                    return $this->redirectToRoute('coreshop_index');
                }

                $customer->setPasswordResetHash($this->generateResetPasswordHash($customer));
                $customer->save();

                $resetLink = $this->generateCoreShopUrl(null, 'coreshop_customer_password_reset', ['token' => $customer->getPasswordResetHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch(new RequestPasswordChangeEvent($customer, $resetLink), 'coreshop.customer.request_password_reset');

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_request_success'));

                return $this->redirectToRoute('coreshop_login');
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Register/password-reset-request.html'), [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function passwordResetAction(Request $request)
    {
        $resetToken = $request->get('token');

        if ($resetToken) {
            $customer = $this->getCustomerRepository()->findByResetToken($resetToken);

            $form = $this->get('form.factory')->createNamed('', ResetPasswordType::class);

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
                $handledForm = $form->handleRequest($request);

                if ($handledForm->isValid()) {
                    $resetPassword = $handledForm->getData();

                    $customer->setPassword($resetPassword['password']);
                    $customer->save();

                    $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_success'));

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch(new GenericEvent($customer), 'coreshop.customer.password_reset');

                    return $this->redirectToRoute('coreshop_login');
                }
            }

            return $this->render($this->templateConfigurator->findTemplate('Register/password-reset.html'), [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    /**
     * @return CustomerRepositoryInterface
     */
    protected function getCustomerRepository()
    {
        return $this->get('coreshop.repository.customer');
    }

    /**
     * @return bool|CustomerInterface|null
     */
    protected function getCustomer()
    {
        try {
            return $this->get(CustomerContextInterface::class)->getCustomer();
        } catch (\Exception $ex) {
        }

        return null;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return string
     */
    protected function generateResetPasswordHash(CustomerInterface $customer)
    {
        $resetIdentifier = $this->container->getParameter('coreshop.customer.security.login_identifier');

        $userKey = $resetIdentifier === 'email' ? $customer->getEmail() : $customer->getUsername();

        return hash('md5', $customer->getId() . $userKey . mt_rand() . time());
    }
}
