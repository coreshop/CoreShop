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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CheckoutIdentifierExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var CheckoutManagerFactoryInterface
     */
    protected $checkoutManagerFactory;

    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @param RequestStack                    $requestStack
     * @param UrlGeneratorInterface           $router
     * @param CheckoutManagerFactoryInterface $checkoutManagerFactory
     * @param CartContextInterface            $cartContext
     */
    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        CheckoutManagerFactoryInterface $checkoutManagerFactory,
        CartContextInterface $cartContext
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->checkoutManagerFactory = $checkoutManagerFactory;
        $this->cartContext = $cartContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('coreshop_checkout_identifier', [$this, 'getIdentifier']),
            new \Twig_Function('coreshop_checkout_get_steps', [$this, 'getSteps'])
        ];
    }

    /**
     * @param string $identifierGuesser
     * @return mixed
     */
    public function getIdentifier($identifierGuesser = '')
    {
        $validGuesser = ['first_step', 'previous_step', 'current_step', 'next_step', 'last_step'];

        if (!in_array($identifierGuesser, $validGuesser)) {
            throw new \InvalidArgumentException(sprintf('invalid identifier guess, available guesses are: %s', implode(', ', $validGuesser)));
        }

        $cart = $this->cartContext->getCart();
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);
        $steps = $checkoutManager->getSteps();

        switch ($identifierGuesser) {
            case 'first_step':
            default:
                $data = reset($steps);
                break;
            case 'last_step':
                $data = end($steps);
                break;
            case 'previous_step':
            case 'current_step':
            case 'next_step':
                $data = null;
                $request = $this->requestStack->getMasterRequest();
                $stepIdentifier = $request->get('stepIdentifier');
                if (!is_null($stepIdentifier)) {
                    if ($identifierGuesser === 'previous_step') {
                        if ($checkoutManager->hasPreviousStep($stepIdentifier)) {
                            $step = $checkoutManager->getPreviousStep($stepIdentifier);
                            $data = $step->getIdentifier();
                        }
                    } elseif ($identifierGuesser === 'current_step') {
                        $data = $stepIdentifier;
                    } elseif ($identifierGuesser === 'next_step') {
                        if ($checkoutManager->hasNextStep($stepIdentifier)) {
                            $step = $checkoutManager->getNextStep($stepIdentifier);
                            $data = $step->getIdentifier();
                        }
                    }
                }
                break;
        }

        return $data;
    }

    public function getSteps()
    {
        $cart = $this->cartContext->getCart();
        $request = $this->requestStack->getMasterRequest();
        $stepIdentifier = $request->get('stepIdentifier');
        $requestAttributes = $request->attributes;
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);
        $currentStep = $checkoutManager->getCurrentStepIndex($stepIdentifier);
        $checkoutSteps = $checkoutManager->getSteps();

        //always add cart to checkout
        $isCartStep = $requestAttributes->get('_route') === 'coreshop_cart_summary';

        //@todo: move this to configuration!
        if (isset($checkoutSteps['cart'])) {
            throw new \InvalidArgumentException('cart is a coreshop reserved checkout step. please rename your checkout step');
        }

        $shopSteps = [
            'cart' => [
                'waiting' => false,
                'done'    => !is_null($stepIdentifier),
                'current' => $requestAttributes->get('_route') === 'coreshop_cart_summary',
                'url'     => $isCartStep
            ]
        ];

        foreach ($checkoutSteps as $identifier) {
            $stepIndex = $checkoutManager->getCurrentStepIndex($identifier);
            $step = $checkoutManager->getStep($identifier);
            $isDone = $step->validate($cart);

            $shopSteps[(string)$identifier] = [
                'waiting' => is_null($stepIdentifier) || (int)$currentStep < $stepIndex,
                'done'    => $isDone,
                'current' => !is_null($stepIdentifier) && (int)$currentStep === $stepIndex,
                'url'     => !is_null($stepIdentifier) ? $this->router->generate('coreshop_checkout', ['stepIdentifier' => (string)$identifier]) : null
            ];
        }

        return $shopSteps;
    }
}
