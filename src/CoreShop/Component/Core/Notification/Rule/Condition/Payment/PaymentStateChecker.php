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

namespace CoreShop\Component\Core\Notification\Rule\Condition\Payment;

use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;

class PaymentStateChecker extends \CoreShop\Component\Core\Notification\Rule\Condition\Order\PaymentStateChecker
{
    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param PimcoreRepositoryInterface $orderRepository
     */
    public function __construct(PimcoreRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof PaymentInterface) {
            return parent::isNotificationRuleValid($this->orderRepository->find($subject->getOrderId()), $params, $configuration);
        }

        return false;
    }
}
