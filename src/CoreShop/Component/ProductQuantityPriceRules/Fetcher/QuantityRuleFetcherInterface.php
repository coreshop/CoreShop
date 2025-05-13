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

namespace CoreShop\Component\ProductQuantityPriceRules\Fetcher;

use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;

interface QuantityRuleFetcherInterface
{
    public function fetch(QuantityRangePriceAwareInterface $subject, array $context): ProductQuantityPriceRuleInterface;

    /**
     * @return array|ProductQuantityPriceRuleInterface[]
     */
    public function getQuantityPriceRulesForSubject(QuantityRangePriceAwareInterface $subject, array $context): array;
}
