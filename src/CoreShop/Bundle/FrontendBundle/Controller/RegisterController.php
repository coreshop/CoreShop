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

use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Bundle\UserBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Bundle\UserBundle\Form\Type\RequestResetPasswordType;
use CoreShop\Bundle\UserBundle\Form\Type\ResetPasswordType;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
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

        if ($customer instanceof CustomerInterface && null === $customer->getUser()) {
            return $this->redirectToRoute('coreshop_customer_profile');
        }

        $form = $this->get('form.factory')->create(CustomerRegistrationType::class, $this->get('coreshop.factory.customer')->createNew());

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

                $user = $this->get('coreshop.repository.user')->findByEmail($passwordReset['email']);

                if (!$user instanceof UserInterface) {
                    return $this->redirectToRoute('coreshop_index');
                }

                $user->setPasswordResetHash(hash('md5', $user->getId() . $user->getEmail() . mt_rand() . time()));
                $user->save();

                $resetLink = $this->generateCoreShopUrl(null, 'coreshop_customer_password_reset', ['token' => $user->getPasswordResetHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('coreshop.user.request_password_reset', new RequestPasswordChangeEvent($user, $resetLink));

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
            /**
             * @var UserInterface $user
             */
            $user = $this->get('coreshop.repository.user')->findByResetToken($resetToken);

            $form = $this->get('form.factory')->createNamed('', ResetPasswordType::class);

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
                $handledForm = $form->handleRequest($request);

                if ($handledForm->isValid()) {
                    $resetPassword = $handledForm->getData();

                    $user->setPassword($resetPassword['password']);
                    $user->save();

                    $this->addFlash('success', $this->get('translator')->trans('coreshop.ui.password_reset_success'));

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('coreshop.user.password_reset', new GenericEvent($user));

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
