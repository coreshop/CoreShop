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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\User\Model\User as BaseUser;

abstract class User extends BaseUser implements UserInterface
{
    public function getUserIdentifier(): string
    {
        return $this->getLoginIdentifier();
    }

    public function getRoles(): array
    {
        $roles = parent::getRoles();

        if (!in_array(static::CORESHOP_ROLE_DEFAULT, $roles, true)) {
            $roles[] = static::CORESHOP_ROLE_DEFAULT;
        }

        if (!$customer = $this->getCustomer()) {
            return $roles;
        }

        if (is_array($customer->getCustomerGroups())) {
            /** @var CustomerGroupInterface $group */
            foreach ($customer->getCustomerGroups() as $group) {
                $groupRoles = $group->getRoles();
                $roles = array_merge($roles, is_array($groupRoles) ? $groupRoles : []);
            }
        }

        return array_unique($roles);
    }
}
