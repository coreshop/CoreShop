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

namespace CoreShop\Model\Order;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Object\Fieldcollection\Data\AbstractData;

/**
 * Class Tax
 * @package CoreShop\Model\Order
 */
class Tax extends AbstractData
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\Fieldcollection\\Data\\CoreShopOrderTax';

    /**
     * Get Tax Name.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getName()
    {
        throw new UnsupportedException('getName is not supported for '.get_class($this));
    }

    /**
     * Set Name.
     *
     * @param string $name
     *
     * @throws UnsupportedException
     */
    public function setName($name)
    {
        throw new UnsupportedException('setName is not supported for '.get_class($this));
    }

    /**
     * Get Rate.
     *
     * @return float
     *
     * @throws UnsupportedException
     */
    public function getRate()
    {
        throw new UnsupportedException('getRate is not supported for '.get_class($this));
    }

    /**
     * Set Rate.
     *
     * @param float $rate
     *
     * @throws UnsupportedException
     */
    public function setRate($rate)
    {
        throw new UnsupportedException('setRate is not supported for '.get_class($this));
    }

    /**
     * Get amount.
     *
     * @return float
     *
     * @throws UnsupportedException
     */
    public function getAmount()
    {
        throw new UnsupportedException('getAmount is not supported for '.get_class($this));
    }

    /**
     * Set Amount.
     *
     * @param float $amount
     *
     * @throws UnsupportedException
     */
    public function setAmount($amount)
    {
        throw new UnsupportedException('setAmount is not supported for '.get_class($this));
    }
}
