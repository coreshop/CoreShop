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

namespace CoreShop\Component\Order\Processor;

use CoreShop\Component\Order\Model\CartInterface;
use Zend\Stdlib\PriorityQueue;

final class CompositeCartProcessor implements CartProcessorInterface
{
    /**
     * @var PriorityQueue|CartProcessorInterface[]
     */
    private $cartProcessors;

    public function __construct()
    {
        $this->cartProcessors = new PriorityQueue();
    }

    /**
     * @param CartProcessorInterface $cartProcessor
     * @param int                    $priority
     */
    public function addProcessor(CartProcessorInterface $cartProcessor, $priority = 0)
    {
        $this->cartProcessors->insert($cartProcessor, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartInterface $cart)
    {
        foreach ($this->cartProcessors as $cartProcessor) {
            $cartProcessor->process($cart);
        }
    }
}
