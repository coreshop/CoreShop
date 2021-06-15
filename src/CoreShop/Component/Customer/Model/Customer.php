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

declare(strict_types=1);

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Pimcore\Model\DataObject\ClassDefinition\Data\Password;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Customer extends AbstractPimcoreModel implements CustomerInterface
{
    private array $roles = [];

    public function getPasswordHasherName(): ?string
    {
        return 'coreshop_customer';
    }

    public function setUsername(?string $username)
    {
        $this->setEmail($username);
    }

    public function getUsername()
    {
        //This is just a fallback, if you want to use username for login, this method is overwritten by Pimcore's implementation
        return $this->getEmail();
    }

    public function getSalt()
    {
        // user has no salt as we use password_hash
        // which handles the salt by itself
        return null;
    }

    /**
     * Trigger the hash calculation to remove the plain text password from the instance. This
     * is necessary to make sure no plain text passwords are serialized.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        /** @var Password $field */
        $field = $this->getClass()->getFieldDefinition('password');
        $field->getDataForResource($this->getPassword(), $this);
    }

    public function getRoles()
    {
        $roles = $this->roles;

        if (is_array($this->getCustomerGroups())) {
            /** @var CustomerGroupInterface $group */
            foreach ($this->getCustomerGroups() as $group) {
                $groupRoles = $group->getRoles();
                $roles = array_merge($roles, is_array($groupRoles) ? $groupRoles : []);
            }
        }

        // we need to make sure to have at least one role
        $roles[] = static::CORESHOP_ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function isEqualTo(UserInterface $user)
    {
        return $user instanceof self && $user->getId() === $this->getId();
    }
}
