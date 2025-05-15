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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Processor\CompositeCartProcessor;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class RegisterCartProcessorPass extends PrioritizedCompositeServicePass
{
    public const CART_PROCESSOR_TAG = 'coreshop.cart_processor';

    public function __construct(
        ) {
        parent::__construct(
            CartProcessorInterface::class,
            CompositeCartProcessor::class,
            self::CART_PROCESSOR_TAG,
            'addProcessor',
        );
    }
}
