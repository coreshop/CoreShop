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

namespace CoreShop\Component\User\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Pimcore\Model\DataObject\ClassDefinition\Data\Password;

abstract class User extends AbstractPimcoreModel implements UserInterface
{
    protected ?string $salt;
    private array $roles = [];
    protected ?string $plainPassword;

    public function setPlainPassword(string $password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setSalt(string $salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt(): ?string
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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function getUsername(): ?string
    {
        return $this->getLoginIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user): bool
    {
        return $user instanceof self && $user->getId() === $this->getId();
    }
}
