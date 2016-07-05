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

use CoreShop\Model\Base;
use CoreShop\Model\Order;
use Pimcore\Model\Object;

/**
 * Class Payment
 * @package CoreShop\Model\Order
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

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
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
}
