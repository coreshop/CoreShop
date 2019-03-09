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

namespace CoreShop\Bundle\ProductBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductUnitDefinitionsHelper extends Helper implements ProductUnitDefinitionsHelperInterface
{
    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(ShopperContextInterface $shopperContext)
    {
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAvailableUnitDefinitions(ProductInterface $product)
    {
        return $product->hasUnitDefinitions();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_unit_definitions';
    }
}
