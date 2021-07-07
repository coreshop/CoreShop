<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\User\Model\User as BaseUser;

abstract class User extends BaseUser implements UserInterface
{
    public function getRoles(): array
    {
        $roles = parent::getRoles();

        if (!in_array(static::CORESHOP_ROLE_DEFAULT, $roles, true)) {
            $roles[] = static::CORESHOP_ROLE_DEFAULT;
        }

        if (!$customer = $this->getCustomer()) {
            return $roles;
        }

        /** @var CustomerGroupInterface $group */
        if (is_array($customer->getCustomerGroups())) {
            foreach ($customer->getCustomerGroups() as $group) {
                $groupRoles = $group->getRoles();
                $roles = array_merge($roles, is_array($groupRoles) ? $groupRoles : []);
            }
        }

        return array_unique($roles);
    }
}
