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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception\ObjectUnsupportedException;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Object\Fieldcollection\Data\AbstractData;

/**
 * Class Tax
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Order
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
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getName()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $name
     *
     * @throws ObjectUnsupportedException
     */
    public function setName($name)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getRate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $rate
     *
     * @throws ObjectUnsupportedException
     */
    public function setRate($rate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getAmount()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $amount
     *
     * @throws ObjectUnsupportedException
     */
    public function setAmount($amount)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
