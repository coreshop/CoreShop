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

namespace CoreShop\Bundle\OrderBundle\EventListener;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use Pimcore\Model\Version;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestCartRecalculation
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param CartManagerInterface $cartManager
     */
    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * Force Cart to be recalculated
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event) {
        if (!$event->isMasterRequest()) {
            return;
        }

        $cart = $this->cartManager->getCart();

        //This could lead to performance issues,
        //The main issue is: when should a cart get recalculated
        //when a price-rule gets invalid? we actually don't know
        //when a price-rule changes or gets invalid
        //Maybe a CronJob to recalculate carts every 30 min?
        if ($cart->getId()) {
            Version::disable();
            $cart->save();
            Version::enable();
        }
    }
}