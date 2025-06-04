<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Notification\Rule\Condition\User;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;

class UserTypeChecker extends AbstractConditionChecker
{
    public const TYPE_REGISTER = 'register';

    public const TYPE_PASSWORD_RESET = 'password-reset';

    public const TYPE_NEWSLETTER_DOUBLE_OPT_IN = 'newsletter-double-opt-in';

    public const TYPE_NEWSLETTER_CONFIRMED = 'newsletter-confirmed';

    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        if ($subject instanceof CustomerInterface) {
            $paramsToExist = [
                'type',
            ];

            foreach ($paramsToExist as $paramToExist) {
                if (!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            $type = $params['type'];

            if ($configuration['userType'] === $type) {
                return true;
            }
        }

        return false;
    }
}
