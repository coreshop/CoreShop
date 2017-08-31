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

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class CartRecalculationUpdate
{
    /**
     * @var CartProcessorInterface
     */
    private $cartProcessor;

    /**
     * @param CartProcessorInterface $cartProcessor
     */
    public function __construct(CartProcessorInterface $cartProcessor)
    {
        $this->cartProcessor = $cartProcessor;
    }

    /**
     * @param DataObjectEvent $event
     */
    public function recalculateCart(DataObjectEvent $event)
    {
        $cart = $event->getObject();

        if (!$cart instanceof CartInterface) {
            return;
        }

        $this->cartProcessor->process($cart);
    }
}