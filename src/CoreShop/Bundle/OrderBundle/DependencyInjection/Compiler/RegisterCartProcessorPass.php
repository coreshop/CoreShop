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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use CoreShop\Component\Order\Processor\CompositeCartProcessor;

final class RegisterCartProcessorPass extends PrioritizedCompositeServicePass
{
    public const CART_PROCESSOR_TAG = 'coreshop.cart_processor';

    public function __construct()
    {
        parent::__construct(
            'coreshop.cart_processor',
            CompositeCartProcessor::class,
            self::CART_PROCESSOR_TAG,
            'addProcessor'
        );
    }
}
