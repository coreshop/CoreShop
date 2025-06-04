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

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler;

use CoreShop\Component\Customer\Context\RequestBased\CompositeRequestResolver;
use CoreShop\Component\Customer\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const CUSTOMER_REQUEST_RESOLVER_SERVICE_TAG = 'coreshop.context.customer.request_based.resolver';

    public function __construct(
        ) {
        parent::__construct(
            RequestResolverInterface::class,
            CompositeRequestResolver::class,
            self::CUSTOMER_REQUEST_RESOLVER_SERVICE_TAG,
            'addResolver',
        );
    }
}
