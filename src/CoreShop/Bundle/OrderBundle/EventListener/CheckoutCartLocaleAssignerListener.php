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

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Event\CheckoutEvent;
use CoreShop\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

class CheckoutCartLocaleAssignerListener
{
    public function __construct(
        private LocaleContextInterface $localeContext,
    ) {
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
