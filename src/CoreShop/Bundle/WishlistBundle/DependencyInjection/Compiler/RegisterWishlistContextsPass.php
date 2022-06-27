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

namespace CoreShop\Bundle\WishlistBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use CoreShop\Component\Wishlist\Context\CompositeWishlistContext;
use CoreShop\Component\Wishlist\Context\WishlistContextInterface;

final class RegisterWishlistContextsPass extends PrioritizedCompositeServicePass
{
    public const WISHLIST_CONTEXT_TAG = 'coreshop.context.wishlist';

    public function __construct()
    {
        parent::__construct(
            WishlistContextInterface::class,
            CompositeWishlistContext::class,
            self::WISHLIST_CONTEXT_TAG,
            'addContext'
        );
    }
}
