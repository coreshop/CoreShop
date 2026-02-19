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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsProductQuantityPriceRuleCalculator
{
    public function __construct(
        private ?string $type = null,
    ) {
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
