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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\SummaryType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\RedirectCheckoutStepInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SummaryCheckoutStep implements CheckoutStepInterface, RedirectCheckoutStepInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private RouterInterface $router,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'summary';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function getResponse(OrderInterface $cart, Request $request): RedirectResponse
    {
        $form = $this->createForm($request, $cart);

        $submitOrder = $form->get('submitOrder');

        $nextAction = $submitOrder instanceof ClickableInterface && $submitOrder->isClicked()
            ? 'coreshop_checkout_do'
            : 'coreshop_cart_create_quote';

        return new RedirectResponse($this->router->generate($nextAction));
    }

    public function validate(OrderInterface $cart): bool
    {
        return true;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                return true;
            }

            throw new CheckoutException('Summary Form is invalid', 'coreshop.ui.error.coreshop_checkout_summary_form_invalid');
        }

        return false;
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        return ['form' => $this->createForm($request, $cart)->createView()];
    }

    private function createForm(Request $request, OrderInterface $cart): FormInterface
    {
        $form = $this->formFactory->createNamed('coreshop', SummaryType::class, $cart);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}
