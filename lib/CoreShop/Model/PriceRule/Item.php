<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\PriceRule;


use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Object\Fieldcollection\Data\AbstractData;

/**
 * Class Fieldcollection
 * @package CoreShop\Model\PriceRule
 */
class Item extends AbstractData {

    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\Fieldcollection\\Data\\CoreShopPriceRuleItem';

    /**
     * @return PriceRule
     *
     * @throws ObjectUnsupportedException
     */
    public function getPriceRule()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param PriceRule $priceRule
     *
     * @throws ObjectUnsupportedException
     */
    public function setPriceRule($priceRule)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getVoucherCode()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $voucherCode
     *
     * @throws ObjectUnsupportedException
     */
    public function setVoucherCode($voucherCode)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return float
     *
     * @throws ObjectUnsupportedException
     */
    public function getDiscount()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param float $discount
     *
     * @throws ObjectUnsupportedException
     */
    public function setDiscount($discount)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

}