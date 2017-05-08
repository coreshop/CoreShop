<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;

class PaymentStateChecker extends AbstractConditionChecker
{
    const PAYMENT_TYPE_PARTIAL = 1;

    const PAYMENT_TYPE_FULL = 2;

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof OrderInterface) {
            if ($configuration['paymentState'] === self::PAYMENT_TYPE_FULL) {
                return $subject->getIsPayed();
            } elseif ($configuration['paymentState'] === self::PAYMENT_TYPE_PARTIAL) {
                $payments = $subject->getPayments();

                return count($payments) > 0;
            }
        }

        return false;
    }
}
