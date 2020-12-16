<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use CoreShop\Component\Store\Context\RequestBased\CompositeRequestResolver;
use CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const STORE_REQUEST_RESOLVER_TAG = 'coreshop.context.store.request_based.resolver';

    public function __construct()
    {
        parent::__construct(
            RequestResolverInterface::class,
            CompositeRequestResolver::class,
            self::STORE_REQUEST_RESOLVER_TAG,
            'addResolver'
        );
    }
}
