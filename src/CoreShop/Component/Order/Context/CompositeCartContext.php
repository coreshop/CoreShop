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

declare(strict_types=1);

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\OrderInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeCartContext implements CartContextInterface
{
    /**
     * @var PriorityQueue|CartContextInterface[]
     * @psalm-var PriorityQueue<CartContextInterface>
     */
    private PriorityQueue $cartContexts;

    public function __construct()
    {
        $this->cartContexts = new PriorityQueue();
    }

    public function addContext(CartContextInterface $cartContext, int $priority = 0): void
    {
        $this->cartContexts->insert($cartContext, $priority);
    }

    public function getCart(): OrderInterface
    {
        foreach ($this->cartContexts as $cartContext) {
            try {
                return $cartContext->getCart();
            } catch (CartNotFoundException $exception) {
                continue;
            }
        }

        throw new CartNotFoundException();
    }
}
