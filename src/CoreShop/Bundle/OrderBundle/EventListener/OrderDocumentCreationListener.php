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

use CoreShop\Bundle\OrderBundle\StateResolver\OrderDocumentStateResolver;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class OrderDocumentCreationListener
{
    /**
     * @var OrderDocumentStateResolver
     */
    private $orderDocumentStateResolver;

    /**
     * @param OrderDocumentStateResolver $orderDocumentStateResolver
     */
    public function __construct(OrderDocumentStateResolver $orderDocumentStateResolver)
    {
        $this->orderDocumentStateResolver = $orderDocumentStateResolver;
    }

    /**
     * @param GenericEvent $event
     */
    public function onOrderDocumentCreated(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), OrderDocumentInterface::class);

        $this->orderDocumentStateResolver->resolve($event->getSubject()->getOrder());
    }
}
