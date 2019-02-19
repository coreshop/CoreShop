<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\User\Model\User as BaseUser;

class User extends BaseUser implements UserInterface
{
    /**
     * @var array
     */
    private $roles = [];

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer($customer)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        /** @var CustomerGroupInterface $group */
        if (is_array($this->getCustomer()->getCustomerGroups())) {
            foreach ($this->getCustomer()->getCustomerGroups() as $group) {
                $groupRoles = $group->getRoles();
                $roles = array_merge($roles, is_array($groupRoles) ? $groupRoles : []);
            }
        }

        // we need to make sure to have at least one role
        $roles[] = static::CORESHOP_ROLE_DEFAULT;

        return array_unique($roles);
    }
}
