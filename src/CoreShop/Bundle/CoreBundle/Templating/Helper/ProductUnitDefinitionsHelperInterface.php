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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Model\ProductInterface;

interface ProductUnitDefinitionsHelperInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasAvailableUnitDefinitions(ProductInterface $product);

    /**
     * Get additional units defined for given product.
     * Does not return unit unless a valid price has been defined.
     *
     * @param ProductInterface $product
     *
     * @return array
     */
    public function getAdditionalUnitDefinitionsWithPrices(ProductInterface $product);
}
