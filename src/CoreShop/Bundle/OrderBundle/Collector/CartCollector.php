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

namespace CoreShop\Bundle\OrderBundle\Collector;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class CartCollector extends DataCollector
{
    private $cartContext;
    private $localeContext;
    private $pimcoreContext;

    public function __construct(
        CartContextInterface $cartContext,
        LocaleContextInterface $localeContext,
        PimcoreContextResolver $pimcoreContext
    ) {
        $this->cartContext = $cartContext;
        $this->localeContext = $localeContext;
        $this->pimcoreContext = $pimcoreContext;

        $this->data = [
            'cart' => null,
            'locale' => 'en',
            'admin' => false,
        ];
    }

    public function getCart(): ?OrderInterface
    {
        return $this->data['cart'];
    }

    public function getLocale(): string
    {
        return $this->data['locale'];
    }

    public function getAdmin(): bool
    {
        return $this->data['admin'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        if ($this->pimcoreContext->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            $this->data['admin'] = true;

            return;
        }

        try {
            $this->data['cart'] = $this->cartContext->getCart();
            $this->data['locale'] = $this->localeContext->getLocaleCode();
        } catch (\Exception $exception) {
            //If something went wrong, we don't have any cart, which we can safely ignore
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'coreshop.cart_collector';
    }
}
