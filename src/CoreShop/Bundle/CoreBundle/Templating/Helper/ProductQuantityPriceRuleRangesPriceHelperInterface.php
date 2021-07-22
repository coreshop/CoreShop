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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

interface ProductQuantityPriceRuleRangesPriceHelperInterface extends HelperInterface
{
    /**
     * @param QuantityRangeInterface $range
     * @param ProductInterface       $product
     * @param array                  $context
     * @param bool                   $withTax
     *
     * @return mixed
     */
    public function getQuantityPriceRuleRangePrice(
        QuantityRangeInterface $range,
        ProductInterface $product,
        array $context,
        bool $withTax = true
    );
}
