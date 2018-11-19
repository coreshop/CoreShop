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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\Helper\Helper;

class CheckoutIdentifierHelper extends Helper implements CheckoutIdentifierHelperInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var LinkGeneratorInterface
     */
    protected $linkGenerator;

    /**
     * @var CheckoutManagerFactoryInterface
     */
    protected $checkoutManagerFactory;

    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @param RequestStack $requestStack
     * @param LinkGeneratorInterface $linkGenerator
     * @param CheckoutManagerFactoryInterface $checkoutManagerFactory
     * @param CartContextInterface $cartContext
     */
    public function __construct(
        RequestStack $requestStack,
        LinkGeneratorInterface $linkGenerator,
        CheckoutManagerFactoryInterface $checkoutManagerFactory,
        CartContextInterface $cartContext
    )
    {
        $this->requestStack = $requestStack;
        $this->linkGenerator = $linkGenerator;
        $this->checkoutManagerFactory = $checkoutManagerFactory;
        $this->cartContext = $cartContext;
    }

    /**
     * Get all Steps of Checkout (cart is always first step here)
     *
     * @return array
     */
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
        $shopSteps = [
            'cart' => [
                'waiting' => false,
                'done' => null !== $stepIdentifier,
                'current' => $requestAttributes->get('_route') === 'coreshop_cart_summary',
                'valid' => null !== $stepIdentifier,
                'url' => $this->linkGenerator->generate($cart, 'coreshop_cart_summary')

            ]
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
                'url' => $this->linkGenerator->generate($cart, 'coreshop_checkout', ['stepIdentifier' => $identifier])
            ];
        }

        return $shopSteps;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getStep($type = '')
    {
        $validGuesser = ['get_first', 'get_previous', 'get_current', 'get_next', 'get_last'];

        $getter = lcfirst(str_replace('_', '', ucwords($type, '_'))) . 'StepIdentifier';
        if (!method_exists($this, $getter)) {
            throw new \InvalidArgumentException(sprintf('invalid identifier guess "%s", available guesses are: %s', $type, implode(', ', $validGuesser)));
        }

        $cart = $this->cartContext->getCart();
        $checkoutManager = $this->checkoutManagerFactory->createCheckoutManager($cart);
        $request = $this->requestStack->getMasterRequest();
        $stepIdentifier = $request->get('stepIdentifier');

        return $this->$getter($cart, $stepIdentifier, $checkoutManager);

    }

    /**
     * @param CartInterface $cart
     * @param string $stepIdentifier
     * @param CheckoutManagerInterface $checkoutManager
     * @return mixed
     */
    protected function getCurrentStepIdentifier($cart, $stepIdentifier, $checkoutManager)
    {
        return $stepIdentifier;

    }

    /**
     * @param CartInterface $cart
     * @param string $stepIdentifier
     * @param CheckoutManagerInterface $checkoutManager
     * @return mixed
     */
    protected function getFirstStepIdentifier($cart, $stepIdentifier, $checkoutManager)
    {
        $steps = $checkoutManager->getSteps();
        return reset($steps);

    }

    /**
     * @param CartInterface $cart
     * @param string $stepIdentifier
     * @param CheckoutManagerInterface $checkoutManager
     * @return mixed
     */
    protected function getLastStepIdentifier($cart, $stepIdentifier, $checkoutManager)
    {
        $steps = $checkoutManager->getSteps();
        return end($steps);
    }

    /**
     * @param CartInterface $cart
     * @param string $stepIdentifier
     * @param CheckoutManagerInterface $checkoutManager
     * @return mixed
     */
    protected function getPreviousStepIdentifier($cart, $stepIdentifier, $checkoutManager)
    {
        $identifier = null;
        $request = $this->requestStack->getMasterRequest();
        $stepIdentifier = $request->get('stepIdentifier');
        if (!is_null($stepIdentifier)) {
            if ($checkoutManager->hasPreviousStep($stepIdentifier)) {
                $step = $checkoutManager->getPreviousStep($stepIdentifier);
                $identifier = $step->getIdentifier();
            }
        }
        return $identifier;
    }

    /**
     * @param CartInterface $cart
     * @param string $stepIdentifier
     * @param CheckoutManagerInterface $checkoutManager
     * @return mixed
     */
    protected function getNextStepIdentifier($cart, $stepIdentifier, $checkoutManager)
    {
        $identifier = null;
        if (!is_null($stepIdentifier)) {
            if ($checkoutManager->hasNextStep($stepIdentifier)) {
                $step = $checkoutManager->getNextStep($stepIdentifier);
                $identifier = $step->getIdentifier();
            }
        }

        return $identifier;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_checkout_identifier';
    }
}
