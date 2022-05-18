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

namespace CoreShop\Component\Wishlist\Processor;

use CoreShop\Component\Wishlist\Model\WishlistInterface;
use Laminas\Stdlib\PriorityQueue;

final class CompositeWishlistProcessor implements WishlistProcessorInterface
{
    private PriorityQueue $wishlistProcessors;

    public function __construct()
    {
        $this->wishlistProcessors = new PriorityQueue();
    }

    public function addProcessor(WishlistProcessorInterface $wishlistProcessor, int $priority = 0): void
    {
        $this->wishlistProcessors->insert($wishlistProcessor, $priority);
    }

    public function process(WishlistInterface $wishlist): void
    {
        foreach ($this->wishlistProcessors as $wishlistProcessor) {
            $wishlistProcessor->process($wishlist);
        }
    }
}
