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

namespace CoreShop\Bundle\PimcoreBundle\Security;

use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class PasswordFieldHasher implements PasswordHasherInterface
{
    protected string $className;
    protected string $fieldName = 'password';
    protected ?ClassDefinition $classDefinition = null;
    protected bool $updateHash = true;

    public function __construct(string $className, string $fieldName)
    {
        $this->className = $className;
        $this->fieldName = $fieldName;
    }

    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new BadCredentialsException(sprintf('Password exceeds a maximum of %d characters', static::MAX_PASSWORD_LENGTH));
        }

        return $this->getClassDefinition()->getFieldDefinition($this->fieldName)->calculateHash($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        $hash = $this->hash($plainPassword);

        return $hash === $hashedPassword;
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }

    protected function isPasswordTooLong(string $password)
    {
        return \strlen($password) > static::MAX_PASSWORD_LENGTH;
    }

    protected function getClassDefinition()
    {
        if (null === $this->classDefinition) {
            $this->classDefinition = ClassDefinition::getByName($this->className);
        }

        return $this->classDefinition;
    }
}
