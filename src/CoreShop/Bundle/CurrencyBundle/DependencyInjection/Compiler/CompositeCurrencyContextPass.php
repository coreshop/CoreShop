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

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use CoreShop\Component\Currency\Context\CompositeCurrencyContext;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public const CURRENCY_CONTEXT_SERVICE_TAG = 'coreshop.context.currency';

    public function __construct(
        ) {
        parent::__construct(
            CurrencyContextInterface::class,
            CompositeCurrencyContext::class,
            self::CURRENCY_CONTEXT_SERVICE_TAG,
            'addContext',
        );
    }
}
