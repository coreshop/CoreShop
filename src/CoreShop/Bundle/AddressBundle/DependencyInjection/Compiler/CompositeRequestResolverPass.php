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

namespace CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public const COUNTRY_REQUEST_RESOLVER_SERVICE_TAG = 'coreshop.context.country.request_based.resolver';

    public function __construct()
    {
        parent::__construct(
            'coreshop.context.country.request_based.resolver',
            'coreshop.context.country.request_based.resolver.composite',
            self::COUNTRY_REQUEST_RESOLVER_SERVICE_TAG,
            'addResolver'
        );
    }
}
