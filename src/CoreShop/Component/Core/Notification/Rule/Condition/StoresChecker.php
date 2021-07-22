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

declare(strict_types=1);

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class StoresChecker extends AbstractConditionChecker
{
    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        $store = null;

        if ($subject instanceof StoreAwareInterface) {
            $store = $subject->getStore();
        } elseif ($subject instanceof OrderDocumentInterface) {
            $store = $subject->getOrder()->getStore();
        } elseif ($subject instanceof PaymentInterface) {
            $order = $subject->getOrder();

            if ($order instanceof OrderInterface) {
                $store = $order->getStore();
            }
        }

        if ($store instanceof StoreInterface) {
            return in_array($store->getId(), $configuration['stores']);
        }

        return false;
    }
}
