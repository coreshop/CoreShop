<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductQuantityPriceRuleRangesPriceHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductQuantityPriceRuleRangesPriceExtension extends AbstractExtension
{
    /**
     * @var ProductQuantityPriceRuleRangesPriceHelperInterface
     */
    private $helper;

    /**
     * @param ProductQuantityPriceRuleRangesPriceHelperInterface $helper
     */
    public function __construct(ProductQuantityPriceRuleRangesPriceHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('coreshop_quantity_price_rule_range_price', [$this->helper, 'getQuantityPriceRuleRangePrice']),
        ];
    }
}
