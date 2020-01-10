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

use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerLoginType;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends FrontendController
{
    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @param AuthenticationUtils     $authenticationUtils
     * @param FormFactoryInterface    $formFactory
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        ShopperContextInterface $shopperContext
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->shopperContext = $shopperContext;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        if ($this->shopperContext->hasCustomer() && $this->shopperContext->getCustomer()->getIsGuest() === false) {
            return $this->redirectToRoute('coreshop_index');
        }

        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->formFactory->createNamed('', CustomerLoginType::class);

        $renderLayout = $request->get('renderLayout', true);

        $viewWithLayout = $this->templateConfigurator->findTemplate('Security/login.html');
        $viewWithoutLayout = $this->templateConfigurator->findTemplate('Security/_login-form.html');

        return $this->renderTemplate($renderLayout ? $viewWithLayout : $viewWithoutLayout, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
            'target' => $request->get('target', null),
            'failure' => $request->get('failure', null),
        ]);
    }

    /**
     * @param Request $request
     */
    public function checkAction(Request $request)
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }
}
