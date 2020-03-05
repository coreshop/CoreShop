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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface SaleItemInterface extends ProposalItemInterface, PimcoreModelInterface, BaseAdjustableInterface
{
    /**
     * @param string $language
     *
     * @return string
     */
    public function getName($language = null);

    /**
     * @param string $name
     * @param string $language
     *
     * @return mixed
     */
    public function setName($name, $language = null);

    /**
     * @return bool
     */
    public function getIsGiftItem();

    /**
     * @return SaleInterface
     */
    public function getSaleDocument();

    /**
     * @return int
     */
    public function getBaseItemWholesalePrice();

    /**
     * @param int $baseItemWholesalePrice
     */
    public function setBaseItemWholesalePrice($baseItemWholesalePrice);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemPrice($withTax = true);

    /**
     * @param int  $itemPrice
     * @param bool $withTax
     */
    public function setBaseItemPrice($itemPrice, $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseItemRetailPrice($withTax = true);

    /**
     * @param int  $itemRetailPrice
     * @param bool $withTax
     */
    public function setBaseItemRetailPrice($itemRetailPrice, $withTax = true);

    /**
     * @return int
     */
    public function getBaseItemTax();

    /**
     * @param int $itemTax
     */
    public function setBaseItemTax($itemTax);

    /**
     * @return mixed
     */
    public function getBaseTaxes();

    /**
     * @param mixed $taxes
     */
    public function setBaseTaxes($taxes);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseTotal($withTax = true);

    /**
     * @param int  $total
     * @param bool $withTax
     */
    public function setBaseTotal($total, $withTax = true);
}
