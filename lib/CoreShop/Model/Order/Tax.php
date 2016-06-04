<?php

namespace CoreShop\Model\Order;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Object\Fieldcollection\Data\AbstractData;

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
