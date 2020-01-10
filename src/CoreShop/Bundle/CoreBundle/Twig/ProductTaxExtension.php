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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductTaxHelperInterface;

final class ProductTaxExtension extends \Twig_Extension
{
    /**
     * @var ProductTaxHelperInterface
     */
    private $productTaxHelper;

    /**
     * @param ProductTaxHelperInterface $productTaxHelper
     */
    public function __construct(ProductTaxHelperInterface $productTaxHelper)
    {
        $this->productTaxHelper = $productTaxHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_product_tax_amount', [$this->productTaxHelper, 'getTaxAmount']),
            new \Twig_Filter('coreshop_product_tax_rate', [$this->productTaxHelper, 'getTaxRate']),
        ];
    }
}
