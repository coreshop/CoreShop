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

namespace CoreShop\Model\Mail\Rule\Condition\Order;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class PaymentState
 * @package CoreShop\Model\Mail\Rule\Condition\Order
 */
class PaymentState extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'paymentState';

    /**
     *
     */
    const PAYMENT_TYPE_PARTIAL = 1;

    /**
     *
     */
    const PAYMENT_TYPE_FULL = 2;

    /**
     * @var int
     */
    public $paymentState;

    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if ($object instanceof Model\Order) {
            if ($this->getPaymentState() === self::PAYMENT_TYPE_FULL) {
                return $object->getIsPayed();
            } elseif ($this->getPaymentState() === self::PAYMENT_TYPE_PARTIAL) {
                $payments = $object->getPayments();

                return count($payments) > 0;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * @param int $paymentState
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;
    }
}
