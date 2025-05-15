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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use CoreShop\Component\Store\Context\RequestBased\CompositeRequestResolver;
use CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const STORE_REQUEST_RESOLVER_TAG = 'coreshop.context.store.request_based.resolver';

    public function __construct(
        ) {
        parent::__construct(
            RequestResolverInterface::class,
            CompositeRequestResolver::class,
            self::STORE_REQUEST_RESOLVER_TAG,
            'addResolver',
        );
    }
}
