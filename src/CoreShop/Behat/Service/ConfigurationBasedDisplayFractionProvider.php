<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Service;

use CoreShop\Component\Currency\Display\DisplayFractionProviderInterface;

class ConfigurationBasedDisplayFractionProvider implements DisplayFractionProviderInterface
{
    public function __construct(
        protected int $decimalPrecision,
    ) {
    }

    public function getDisplayFraction(array $context): int
    {
        return $this->decimalPrecision;
    }
}
