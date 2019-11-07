<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const CUSTOMER_REQUEST_RESOLVER_SERVICE_TAG = 'coreshop.context.customer.request_based.resolver';

    public function __construct()
    {
        parent::__construct(
            'coreshop.context.customer.request_based.resolver',
            'coreshop.context.customer.request_based.resolver.composite',
            self::CUSTOMER_REQUEST_RESOLVER_SERVICE_TAG,
            'addResolver'
        );
    }
}
