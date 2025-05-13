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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\CoreBundle\Customer\CustomerManagerInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Bundle\UserBundle\Event\RequestPasswordChangeEvent;
use CoreShop\Bundle\UserBundle\Form\Type\RequestResetPasswordType;
use CoreShop\Bundle\UserBundle\Form\Type\ResetPasswordType;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class RegisterController extends FrontendController
{
    public function registerAction(Request $request): Response
    {
        $customer = $this->getCustomer();

        if ($customer instanceof CustomerInterface && null !== $customer->getUser()) {
            return $this->redirectToRoute('coreshop_customer_profile');
        }

        $form = $this->container->get('form.factory')->createNamed('customer', CustomerRegistrationType::class, $this->container->get('coreshop.factory.customer')->createNew());

        $redirect = $this->getParameterFromRequest($request, '_redirect', $this->generateUrl('coreshop_customer_profile'));

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $form = $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $customer = $form->getData();
                $customer->setLocaleCode($this->container->get('coreshop.context.locale')->getLocaleCode());

                $this->container->get(CustomerManagerInterface::class)->persistCustomer($customer);

                return $this->redirect($redirect);
            }
        }

        return $this->render($this->getTemplateConfigurator()->findTemplate('Register/register.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function passwordResetRequestAction(Request $request): Response
    {
        $resetIdentifier = $this->getParameter('coreshop.customer.security.login_identifier');
        $form = $this->container->get('form.factory')->createNamed('coreshop', RequestResetPasswordType::class, null, ['reset_identifier' => $resetIdentifier]);

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

                $resetLink = $this->generateUrl('coreshop_customer_password_reset', ['token' => $user->getPasswordResetHash()], UrlGeneratorInterface::ABSOLUTE_URL);

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch(new RequestPasswordChangeEvent($user, $resetLink), 'coreshop.user.request_password_reset');

                $this->addFlash('success', $this->container->get('translator')->trans('coreshop.ui.password_reset_request_success'));

                return $this->redirectToRoute('coreshop_login');
            }
        }

        return $this->render($this->getTemplateConfigurator()->findTemplate('Register/password-reset-request.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function passwordResetAction(Request $request): Response
    {
        $resetToken = $this->getParameterFromRequest($request, 'token');

        if ($resetToken) {
            /**
             * @var UserInterface $user
             */
            $user = $this->container->get('coreshop.repository.user')->findByResetToken($resetToken);

            if (!$user) {
                throw new NotFoundHttpException();
            }

            $form = $this->container->get('form.factory')->createNamed('coreshop', ResetPasswordType::class);

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
                $handledForm = $form->handleRequest($request);

                if ($handledForm->isSubmitted() && $handledForm->isValid()) {
                    $resetPassword = $handledForm->getData();

                    $user->setPasswordResetHash(null);
                    $user->setPassword($resetPassword['password']);
                    $user->save();

                    $this->addFlash('success', $this->container->get('translator')->trans('coreshop.ui.password_reset_success'));

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch(new GenericEvent($user), 'coreshop.user.password_reset');

                    return $this->redirectToRoute('coreshop_login');
                }
            }

            return $this->render($this->getTemplateConfigurator()->findTemplate('Register/password-reset.html'), [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                new SubscribedService('coreshop.factory.customer', FactoryInterface::class, attributes: new Autowire(service: 'coreshop.factory.customer')),
                new SubscribedService('coreshop.repository.user', RepositoryInterface::class, attributes: new Autowire(service: 'coreshop.repository.user')),
                new SubscribedService('coreshop.context.locale', LocaleContextInterface::class),
                new SubscribedService(CustomerManagerInterface::class, CustomerManagerInterface::class),
                new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
            ],
        );
    }

    protected function getCustomer(): ?CustomerInterface
    {
        try {
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->container->get(CustomerContextInterface::class)->getCustomer();

            return $customer;
        } catch (\Exception) {
        }

        return null;
    }

    protected function generateResetPasswordHash(UserInterface $customer): string
    {
        $this->getParameter('coreshop.customer.security.login_identifier');

        return hash('md5', $customer->getId() . $customer->getLoginIdentifier() . mt_rand() . time());
    }
}
