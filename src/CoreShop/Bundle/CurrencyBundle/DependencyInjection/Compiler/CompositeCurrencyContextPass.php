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

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;
use CoreShop\Component\Currency\Context\CompositeCurrencyContext;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;

final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public const CURRENCY_CONTEXT_SERVICE_TAG = 'coreshop.context.currency';

    public function __construct()
    {
        parent::__construct(
            CurrencyContextInterface::class,
            CompositeCurrencyContext::class,
            self::CURRENCY_CONTEXT_SERVICE_TAG,
            'addContext'
        );
    }
}
