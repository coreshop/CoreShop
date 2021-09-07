<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CheckoutIdentifierExtension extends AbstractExtension
{
    private RequestStack $requestStack;
    private LinkGeneratorInterface $linkGenerator;
    private CheckoutManagerFactoryInterface $checkoutManagerFactory;
    private CartContextInterface $cartContext;

    public function __construct(
        RequestStack $requestStack,
        LinkGeneratorInterface $linkGenerator,
        CheckoutManagerFactoryInterface $checkoutManagerFactory,
        CartContextInterface $cartContext
    ) {
        $this->requestStack = $requestStack;
        $this->linkGenerator = $linkGenerator;
        $this->checkoutManagerFactory = $checkoutManagerFactory;
        $this->cartContext = $cartContext;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_checkout_steps', [$this, 'getSteps']),
            new TwigFunction('coreshop_checkout_steps_*', [$this, 'getStep']),
        ];
    }

    public function getSteps(): array
    {
        $cart = $this->cartContext->getCart();
        $request = $this->requestStack->getMasterRequest();

        if (!$request) {
            return [];
        }

        $stepIdentifier = $request->get('stepIdentifier');
        $requestAttributes = $request->attributes;
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);
        $currentStep = 0;

        if ($stepIdentifier) {
            $currentStep = $checkoutManager->getCurrentStepIndex($stepIdentifier);
        }

        $checkoutSteps = $checkoutManager->getSteps();

        //always add cart to checkout
        $shopSteps = [
            'cart' => [
                'waiting' => false,
                'done' => null !== $stepIdentifier,
                'current' => $requestAttributes->get('_route') === 'coreshop_cart_summary',
                'valid' => null !== $stepIdentifier,
                'url' => $this->linkGenerator->generate($cart, 'coreshop_cart_summary'),
            ],
        ];

        foreach ($checkoutSteps as $identifier) {
            $stepIndex = $checkoutManager->getCurrentStepIndex($identifier);
            $step = $checkoutManager->getStep($identifier);
            $isValid = $step instanceof ValidationCheckoutStepInterface ? $step->validate($cart) : false;

            $shopSteps[$identifier] = [
                'waiting' => null === $stepIdentifier || $currentStep < $stepIndex,
                'done' => null !== $stepIdentifier && $currentStep > $stepIndex,
                'current' => null !== $stepIdentifier && $currentStep === $stepIndex,
                'valid' => $isValid,
                'url' => $this->linkGenerator->generate($cart, 'coreshop_checkout', ['stepIdentifier' => $identifier]),
            ];
        }

        return $shopSteps;
    }

    public function getStep(string $type = ''): ?string
    {
        $validGuesser = ['get_first', 'get_previous', 'get_current', 'get_next', 'get_last'];

        $getter = lcfirst(str_replace('_', '', ucwords($type, '_'))).'StepIdentifier';

        if (!method_exists($this, $getter)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'invalid identifier guess "%s", available guesses are: %s',
                    $type,
                    implode(', ', $validGuesser)
                )
            );
        }

        $cart = $this->cartContext->getCart();
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);
        $request = $this->requestStack->getMasterRequest();

        if (!$request) {
            return null;
        }

        $stepIdentifier = $request->get('stepIdentifier');

        return $this->$getter($cart, $stepIdentifier, $checkoutManager);
    }

    protected function getPreviousStepIdentifier(
        OrderInterface $cart,
        ?string $stepIdentifier,
        CheckoutManagerInterface $checkoutManager
    ): ?string {
        $identifier = null;
        $request = $this->requestStack->getMasterRequest();
        $previousIdentifier = $request->get('stepIdentifier');

        if (null !== $previousIdentifier && $checkoutManager->hasPreviousStep($previousIdentifier)) {
            $step = $checkoutManager->getPreviousStep($previousIdentifier);

            if ($step) {
                return $step->getIdentifier();
            }
        }

        return null;
    }


    protected function getCurrentStepIdentifier(
        OrderInterface $cart,
        ?string $stepIdentifier,
        CheckoutManagerInterface $checkoutManager
    ): ?string {
        return $stepIdentifier;
    }

    protected function getFirstStepIdentifier(
        OrderInterface $cart,
        ?string $stepIdentifier,
        CheckoutManagerInterface $checkoutManager
    ): string {
        $steps = $checkoutManager->getSteps();

        return reset($steps);
    }

    protected function getLastStepIdentifier(
        OrderInterface $cart,
        ?string $stepIdentifier,
        CheckoutManagerInterface $checkoutManager
    ): string {
        $steps = $checkoutManager->getSteps();

        return end($steps);
    }

    protected function getNextStepIdentifier(
        OrderInterface $cart,
        ?string $stepIdentifier,
        CheckoutManagerInterface $checkoutManager
    ): ?string {
        $identifier = null;

        if (null !== $stepIdentifier && $checkoutManager->hasNextStep($stepIdentifier)) {
            $step = $checkoutManager->getNextStep($stepIdentifier);

            if (null !== $step) {
                $identifier = $step->getIdentifier();
            }
        }

        return $identifier;
    }
}
