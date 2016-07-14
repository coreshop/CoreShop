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

use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Base;
use CoreShop\Model\Order;
use Pimcore\Date;
use Pimcore\Model\Object;

/**
 * Class Payment
 * @package CoreShop\Model\Order
 * 
 * @method static Object\Listing\Concrete getByProvider ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByAmount ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTransactionIdentifier ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPayed ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByDatePayment ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentInformation ($value, $limit = 0)
 */
class Payment extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopPayment';

    /**
     * Return Payment by transaction identifier.
     *
     * @param $transactionIdentification
     *
     * @return bool|Payment
     */
    public static function findByTransactionIdentifier($transactionIdentification)
    {
        $list = Payment::getByTransactionIdentifier($transactionIdentification);

        $payments = $list->load();

        if (count($payments) > 0) {
            return $payments[0];
        }

        return false;
    }

    /**
     * Get Order for Payment.
     *
     * @return bool|Object\AbstractObject
     */
    public function getOrder()
    {
        $parent = $this->getParent();

        do {
            if ($parent instanceof Order) {
                return $parent;
            }

            $parent = $parent->getParent();
        } while ($parent != null);

        return false;
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getProvider()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $provider
     *
     * @throws ObjectUnsupportedException
     */
    public function setProvider($provider)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getAmount()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $amount
     *
     * @throws ObjectUnsupportedException
     */
    public function setAmount($amount)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getTransactionIdentifier()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $transactionIdentifier
     *
     * @throws ObjectUnsupportedException
     */
    public function setTransactionIdentifier($transactionIdentifier)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return boolean
     *
     * @throws ObjectUnsupportedException
     */
    public function getPayed()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param boolean $payed
     *
     * @throws ObjectUnsupportedException
     */
    public function setPayed($payed)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Date
     *
     * @throws ObjectUnsupportedException
     */
    public function getDatePayment()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Date $datePayment
     *
     * @throws ObjectUnsupportedException
     */
    public function setDatePayment($datePayment)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentInformation()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $paymentInformation
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentInformation($paymentInformation)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
