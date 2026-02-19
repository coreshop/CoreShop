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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ValidPriceRulesExtension extends AbstractExtension
{
    public function __construct(
        protected ValidRulesFetcherInterface $validPriceRulesFetcher,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_price_rules', [$this->validPriceRulesFetcher, 'getValidRules']),
        ];
    }
}
