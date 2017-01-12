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

namespace CoreShop\Model\Mail\Rule\Condition\User;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class UserType
 * @package CoreShop\Model\Mail\Rule\Condition\User
 */
class UserType extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'userType';

    /**
     *
     */
    const TYPE_REGISTER = 'register';

    /**
     *
     */
    const TYPE_PASSWORD_RESET = 'password-reset';

    /**
     * @var string
     */
    public $userType;

    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if ($object instanceof Model\User) {
            $paramsToExist = [
                'type'
            ];

            foreach ($paramsToExist as $paramToExist) {
                if (!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            $type = $params['type'];

            if ($this->getUserType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param string $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }
}
