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

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use CoreShop\Component\Shipping\Validator\CompositeShippableCarrierValidator;

final class CompositeShippableValidatorPass extends PrioritizedCompositeServicePass
{
    public const SHIPABLE_VALIDATOR_TAG = 'coreshop.shipping.carrier.validator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.shipping.carrier.validator',
            CompositeShippableCarrierValidator::class,
            self::SHIPABLE_VALIDATOR_TAG,
            'addValidator'
        );
    }
}
