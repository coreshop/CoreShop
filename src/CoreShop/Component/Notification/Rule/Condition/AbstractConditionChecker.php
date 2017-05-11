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

namespace CoreShop\Component\Notification\Rule\Condition;

abstract class AbstractConditionChecker implements NotificationConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        if (!is_array($subject)) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        if (!array_key_exists('subject', $subject) || !array_key_exists('params', $subject)) {
            throw new \InvalidArgumentException('Notification Rule Condition $subject needs to be an array with values subject and params');
        }

        return $this->isNotificationRuleValid($subject['subject'], $subject['params'], $configuration);
    }
}
