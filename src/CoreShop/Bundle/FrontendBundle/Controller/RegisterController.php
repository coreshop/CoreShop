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

use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Bundle\UserBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Bundle\UserBundle\Form\Type\RequestResetPasswordType;
use CoreShop\Bundle\UserBundle\Form\Type\ResetPasswordType;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterController extends FrontendController
{
    public function registerAction(Request $request): Response
    {
        $customer = $this->getCustomer();

        if ($customer instanceof CustomerInterface && null === $customer->getUser()) {
            return $this->redirectToRoute('coreshop_customer_profile');
        }

        $form = $this->get('form.factory')->createNamed('customer', CustomerRegistrationType::class, $this->get('coreshop.factory.customer')->createNew());

        $redirect = $request->get('_redirect', $this->generateCoreShopUrl(null, 'coreshop_customer_profile'));

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
    }

    public function passwordResetRequestAction(Request $request): Response
    {
        $resetIdentifier = $this->container->getParameter('coreshop.customer.security.login_identifier');
        $form = $this->get('form.factory')->createNamed('coreshop', RequestResetPasswordType::class, null, ['reset_identifier' => $resetIdentifier]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                $passwordResetData = $handledForm->getData();

                $user = $this->container->get('coreshop.repository.user')->findByLoginIdentifier($passwordResetData['email']);

                if (!$user instanceof UserInterface) {
                    return $this->redirectToRoute('coreshop_index');
                }

                $user->setPasswordResetHash($this->generateResetPasswordHash($user));
                $user->save();

                $resetLink = $this->generateCoreShopUrl(null, 'coreshop_customer_password_reset', ['token' => $user->getPasswordResetHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch(new RequestPasswordChangeEvent($user, $resetLink), 'coreshop.user.request_password_reset');

                $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_request_success'));

                return $this->redirectToRoute('coreshop_login');
            }
        }

        return $this->render($this->templateConfigurator->findTemplate('Register/password-reset-request.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function passwordResetAction(Request $request): Response
    {
        $resetToken = $request->get('token');

        if ($resetToken) {
            /**
             * @var UserInterface $user
             */
            $user = $this->get('coreshop.repository.user')->findByResetToken($resetToken);
            $form = $this->get('form.factory')->createNamed('coreshop', ResetPasswordType::class);

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
                $handledForm = $form->handleRequest($request);

                if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                    $resetPassword = $handledForm->getData();

                    $user->setPasswordResetHash(null);
                    $user->setPassword($resetPassword['password']);
                    $user->save();

                    $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_success'));

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch(new GenericEvent($user), 'coreshop.user.password_reset');

                    return $this->redirectToRoute('coreshop_login');
                }
            }

            return $this->render($this->templateConfigurator->findTemplate('Register/password-reset.html'), [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    protected function getCustomer(): ?CustomerInterface
    {
        try {
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->get(CustomerContextInterface::class)->getCustomer();

            return $customer;
        } catch (\Exception $ex) {
        }

        return null;
    }

    protected function generateResetPasswordHash(UserInterface $customer): string
    {
        $resetIdentifier = $this->container->getParameter('coreshop.customer.security.login_identifier');

        return hash('md5', $customer->getId() . $customer->getLoginIdentifier() . mt_rand() . time());
    }
}
