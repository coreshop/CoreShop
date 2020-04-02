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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ValidPriceRulesHelperInterface;
use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ValidPriceRulesExtension extends AbstractExtension
{
    protected $validPriceRulesFetcher;

    public function __construct(ValidRulesFetcherInterface $validPriceRulesFetcher)
    {
        $this->validPriceRulesFetcher = $validPriceRulesFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_product_price_rules', [$this->validPriceRulesFetcher, 'getValidRules']),
        ];
    }
}
