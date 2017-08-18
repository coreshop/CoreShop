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

use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerLoginType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
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
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @param AuthenticationUtils  $authenticationUtils
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface      $templatingEngine
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        EngineInterface $templatingEngine
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->formFactory->createNamed('', CustomerLoginType::class);

        $renderLayout = $request->get('renderLayout', true);

        return $this->renderTemplate($renderLayout ? '@CoreShopFrontend/Security/login.html.twig' : '@CoreShopFrontend/Security/_login-form.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
            'target' => $request->get('target', null),
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
