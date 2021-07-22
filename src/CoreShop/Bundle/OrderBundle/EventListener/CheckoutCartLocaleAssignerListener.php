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

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Event\CheckoutEvent;
use CoreShop\Component\Order\Model\CartInterface;
use Webmozart\Assert\Assert;

class CheckoutCartLocaleAssignerListener
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    public function assignLocaleOnCheckout(CheckoutEvent $event)
    {
        /**
         * @var CartInterface $cart
         */
        $cart = $event->getSubject();

        Assert::isInstanceOf($cart, CartInterface::class);

        $cart->setLocaleCode($this->localeContext->getLocaleCode());
    }
}
