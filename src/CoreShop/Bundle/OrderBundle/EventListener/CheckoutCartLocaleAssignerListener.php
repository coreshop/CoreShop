<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Event\CheckoutEvent;
use CoreShop\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

class CheckoutCartLocaleAssignerListener
{
    private LocaleContextInterface $localeContext;

    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    public function assignLocaleOnCheckout(CheckoutEvent $event): void
    {
        /**
         * @var OrderInterface $cart
         */
        $cart = $event->getSubject();

        Assert::isInstanceOf($cart, OrderInterface::class);

        $cart->setLocaleCode($this->localeContext->getLocaleCode());
    }
}
