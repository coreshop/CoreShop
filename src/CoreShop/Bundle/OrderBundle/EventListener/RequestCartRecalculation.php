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

        if ($cart->getId()) {
            Version::disable();
            $cart->save();
            Version::enable();
        }
    }
}