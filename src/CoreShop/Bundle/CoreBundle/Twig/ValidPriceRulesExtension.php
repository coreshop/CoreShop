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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ValidPriceRulesExtension extends AbstractExtension
{
    public function __construct(protected ValidRulesFetcherInterface $validPriceRulesFetcher)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_price_rules', [$this->validPriceRulesFetcher, 'getValidRules']),
        ];
    }
}
