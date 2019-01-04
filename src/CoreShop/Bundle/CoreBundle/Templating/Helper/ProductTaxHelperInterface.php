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

use CoreShop\Component\Order\Model\PurchasableInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

interface ProductTaxHelperInterface extends HelperInterface
{
    /**
     * @param PurchasableInterface $product
     *
     * @return mixed
     */
    public function getTaxAmount(PurchasableInterface $product);

    /**
     * @param PurchasableInterface $product
     *
     * @return mixed
     */
    public function getTaxRate(PurchasableInterface $product);
}
