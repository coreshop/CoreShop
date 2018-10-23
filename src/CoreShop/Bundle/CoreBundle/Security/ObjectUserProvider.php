<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Security;

use CoreShop\Component\Core\Model\CustomerInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider implements UserProviderInterface
{
    /**
     * The pimcore class name to be used. Needs to be a fully qualified class
     * name (e.g. Pimcore\Model\DataObject\User or your custom user class extending
     * the generated one.
     *
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $usernameField = 'username';

    /**
     * @param string $className
     * @param string $usernameField
     */
    public function __construct($className, $usernameField = 'username')
    {
        $this->className = $className;
        $this->usernameField = $usernameField;
    }

    /**
     * Check if Class is of right type.
     */
    protected function checkClass()
    {
        if (!class_exists($this->className)) {
            throw new InvalidArgumentException(sprintf('User class %s does not exist', $this->className));
        }

        $reflector = new \ReflectionClass($this->className);
        if (!$reflector->isSubclassOf(AbstractObject::class)) {
            throw new InvalidArgumentException(sprintf('User class %s must be a subclass of %s', $this->className, AbstractObject::class));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $this->checkClass();

        /** @var Concrete $class */
        $class = new $this->className();

        $user = null;
        if ($class instanceof CustomerInterface) {
            try {

                $listing = $class::getList([
                    'condition' => sprintf('`%s` = "%s" AND isGuest = 0', $this->usernameField, $username),
                    'limit'     => 1
                ]);

                $objects = $listing->load();
                if (count($objects) > 0) {
                    $user = $objects[0];
                }

            } catch (\Exception $e) {
                // fail silently.
            }

        } else {
            $getter = sprintf('getBy%s', ucfirst($this->usernameField));
            $user = call_user_func_array([$this->className, $getter], [$username, 1]);
        }

        if ($user && $user instanceof $this->className) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('User %s was not found', $username));
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $this->checkClass();

        if (!$user instanceof $this->className || !$user instanceof AbstractObject) {
            throw new UnsupportedUserException();
        }

        $refreshedUser = call_user_func_array([$this->className, 'getById'], [$user->getId()]);

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->className;
    }
}
