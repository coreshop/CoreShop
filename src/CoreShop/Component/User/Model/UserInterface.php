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

namespace CoreShop\Component\User\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends ResourceInterface, PimcoreModelInterface, SymfonyUserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    public const CORESHOP_ROLE_DEFAULT = 'ROLE_USER';

    public function getId(): ?int;

    public function getLoginIdentifier(): ?string;

    public function setLoginIdentifier(?string $loginIdentifer);

    public function getPassword(): ?string;

    public function setPassword(?string $password);

    public function getPlainPassword(): ?string;

    public function getPasswordResetHash(): ?string;

    public function setPasswordResetHash(?string $passwordResetHash);
}
