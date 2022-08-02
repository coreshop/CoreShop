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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Processor\CompositeCartProcessor;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class RegisterCartProcessorPass extends PrioritizedCompositeServicePass
{
    public const CART_PROCESSOR_TAG = 'coreshop.cart_processor';

    public function __construct()
    {
        parent::__construct(
            CartProcessorInterface::class,
            CompositeCartProcessor::class,
            self::CART_PROCESSOR_TAG,
            'addProcessor'
        );
    }
}
