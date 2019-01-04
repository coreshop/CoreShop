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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductPriceHelperInterface;

final class ProductPriceExtension extends \Twig_Extension
{
    /**
     * @var ProductPriceHelperInterface
     */
    private $helper;

    /**
     * @param ProductPriceHelperInterface $helper
     */
    public function __construct(ProductPriceHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_product_price', [$this->helper, 'getPrice'], ['withTax' => ['with_tax']]),
            new \Twig_Filter('coreshop_product_retail_price', [$this->helper, 'getRetailPrice'], ['withTax' => ['with_tax']]),
            new \Twig_Filter('coreshop_product_discount_price', [$this->helper, 'getDiscountPrice'], ['withTax' => ['with_tax']]),
            new \Twig_Filter('coreshop_product_discount', [$this->helper, 'getDiscount'], ['withTax' => ['with_tax']]),
        ];
    }
}
