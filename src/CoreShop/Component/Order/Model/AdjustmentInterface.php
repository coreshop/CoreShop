<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface AdjustmentInterface extends ResourceInterface
{
    const SHIPPING = 'shipping';
    const CART_PRICE_RULE = 'cart_price_rule';

    /**
     * @return AdjustableInterface|null
     */
    public function getAdjustable();

    /**
     * @return string|null
     */
    public function getTypeIdentifier();

    /**
     * @param string|null $typeIdentifier
     */
    public function setTypeIdentifier($typeIdentifier);

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @param string|null $label
     */
    public function setLabel($label);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getAmount($withTax = true);

    /**
     * @param int $grossAmount
     * @param int $netAmount
     */
    public function setAmount(int $grossAmount, int $netAmount);

    /**
     * @return bool
     */
    public function getNeutral();

    /**
     * @param bool $neutral
     */
    public function setNeutral(bool $neutral);

    /**
     * Adjustments with amount < 0 are called "charges".
     *
     * @return bool
     */
    public function isCharge();

    /**
     * Adjustments with amount > 0 are called "credits".
     *
     * @return bool
     */
    public function isCredit();
}
