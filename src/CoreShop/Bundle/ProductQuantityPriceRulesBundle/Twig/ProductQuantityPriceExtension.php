<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Twig;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Templating\Helper\ProductQuantityPriceHelper;

final class ProductQuantityPriceExtension extends \Twig_Extension
{
    /**
     * @var ProductQuantityPriceHelper
     */
    private $helper;

    /**
     * @param ProductQuantityPriceHelper $helper
     */
    public function __construct(ProductQuantityPriceHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('coreshop_quantity_price_rule_ranges_available', [$this->helper, 'hasActiveQuantityPriceRuleRanges']),
            new \Twig_SimpleFunction('coreshop_quantity_price_rule', [$this->helper, 'getQuantityPriceRule']),
            new \Twig_SimpleFunction('coreshop_quantity_price_rule_ranges', [$this->helper, 'getQuantityPriceRuleRanges']),
            new \Twig_SimpleFunction('coreshop_quantity_price_rule_range_price_calculated', [$this->helper, 'getQuantityPriceRuleRangePrice']),
        ];
    }
}
