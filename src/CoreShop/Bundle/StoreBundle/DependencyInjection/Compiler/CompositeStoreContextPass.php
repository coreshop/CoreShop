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

namespace CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use CoreShop\Component\Store\Context\CompositeStoreContext;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class CompositeStoreContextPass extends PrioritizedCompositeServicePass
{
    public const STORE_CONTEXT_TAG = 'coreshop.context.store';

    public function __construct()
    {
        parent::__construct(
            StoreContextInterface::class,
            CompositeStoreContext::class,
            self::STORE_CONTEXT_TAG,
            'addContext'
        );
    }
}
