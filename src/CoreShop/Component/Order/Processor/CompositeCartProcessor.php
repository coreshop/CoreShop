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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Processor;

use CoreShop\Component\Order\Model\OrderInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeCartProcessor implements CartProcessorInterface
{
    private PriorityQueue $cartProcessors;

    public function __construct(
        ) {
        $this->cartProcessors = new PriorityQueue();
    }

    public function addProcessor(CartProcessorInterface $cartProcessor, int $priority = 0): void
    {
        $this->cartProcessors->insert($cartProcessor, $priority);
    }

    public function process(OrderInterface $cart): void
    {
        foreach ($this->cartProcessors as $cartProcessor) {
            $cartProcessor->process($cart);
        }
    }
}
