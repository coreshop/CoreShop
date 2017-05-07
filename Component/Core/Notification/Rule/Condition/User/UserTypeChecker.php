<?php

namespace CoreShop\Component\Core\Notification\Rule\Condition\User;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;

class UserTypeChecker extends AbstractConditionChecker
{
    /**
     *
     */
    const TYPE_REGISTER = 'register';

    /**
     *
     */
    const TYPE_PASSWORD_RESET = 'password-reset';

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        if ($subject instanceof CustomerInterface) {
            $paramsToExist = [
                'type'
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