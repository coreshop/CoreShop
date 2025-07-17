<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Notification\Rule\Condition\User;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;

class UserTypeChecker extends AbstractConditionChecker
{
    public const string TYPE_REGISTER = 'register';

    public const string TYPE_PASSWORD_RESET = 'password-reset';

    public const string TYPE_NEWSLETTER_DOUBLE_OPT_IN = 'newsletter-double-opt-in';

    public const string TYPE_NEWSLETTER_CONFIRMED = 'newsletter-confirmed';

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
