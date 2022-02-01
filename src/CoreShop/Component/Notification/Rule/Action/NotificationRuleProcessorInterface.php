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

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;

interface NotificationRuleProcessorInterface
{
    /**
     * @param mixed                     $subject
     * @param NotificationRuleInterface $rule
     * @param array                     $configuration
     * @param array                     $params
     *
     * @return mixed
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = []);
}
