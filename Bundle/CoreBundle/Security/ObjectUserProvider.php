<?php

namespace CoreShop\Bundle\CoreBundle;

use Pimcore\Model\Object\AbstractObject;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ObjectUserProvider extends UserProviderInterface {
    /**
     * The pimcore class name to be used. Needs to be a fully qualified class
     * name (e.g. Pimcore\Model\Object\User or your custom user class extending
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
     * Check if Class is of right type
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
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        $this->checkClass();

        $getter = sprintf('getBy%s', ucfirst($this->usernameField));

        // User::getByUsername($username, 1);
        $user = call_user_func_array([$this->className, $getter], [$username, 1]);
        if ($user && $user instanceof $this->className) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('User %s was not found', $username));
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return $class === $this->className;
    }
}