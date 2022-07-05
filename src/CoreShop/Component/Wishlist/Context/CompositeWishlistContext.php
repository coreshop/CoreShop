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

namespace CoreShop\Component\Wishlist\Context;

use CoreShop\Component\Wishlist\Model\WishlistInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeWishlistContext implements WishlistContextInterface
{
    /**
     * @var PriorityQueue|WishlistContextInterface[]
     * @psalm-var PriorityQueue<WishlistContextInterface>
     */
    private PriorityQueue $contexts;

    public function __construct()
    {
        $this->contexts = new PriorityQueue();
    }

    public function addContext(WishlistContextInterface $context, int $priority = 0): void
    {
        $this->contexts->insert($context, $priority);
    }

    public function getWishlist(): WishlistInterface
    {
        foreach ($this->contexts as $context) {
            try {
                return $context->getWishlist();
            } catch (WishlistNotFoundException) {
                continue;
            }
        }

        throw new WishlistNotFoundException();
    }
}
