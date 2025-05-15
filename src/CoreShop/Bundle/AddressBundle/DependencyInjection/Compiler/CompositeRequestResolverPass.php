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

namespace CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler;

use CoreShop\Component\Address\Context\RequestBased\CompositeRequestResolver;
use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const COUNTRY_REQUEST_RESOLVER_SERVICE_TAG = 'coreshop.context.country.request_based.resolver';

    public function __construct(
        ) {
        parent::__construct(
            RequestResolverInterface::class,
            CompositeRequestResolver::class,
            self::COUNTRY_REQUEST_RESOLVER_SERVICE_TAG,
            'addResolver',
        );
    }
}
