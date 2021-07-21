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

declare(strict_types=1);

namespace CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler;

use CoreShop\Component\Address\Context\CompositeCountryContext;
use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeCountryContextPass extends PrioritizedCompositeServicePass
{
    public const COUNTRY_CONTEXT_SERVICE_TAG = 'coreshop.context.country';

    public function __construct()
    {
        parent::__construct(
            CountryContextInterface::class,
            CompositeCountryContext::class,
            self::COUNTRY_CONTEXT_SERVICE_TAG,
            'addContext'
        );
    }
}
