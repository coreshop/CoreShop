<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\User\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends ResourceInterface, PimcoreModelInterface, SymfonyUserInterface, EquatableInterface
{
    const CORESHOP_ROLE_DEFAULT = 'ROLE_USER';
    const CORESHOP_ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public function getLoginIdentifier(): ?string;

    public function setLoginIdentifier(?string $loginIdentifer);

    public function getPassword(): ?string;

    public function setPassword(?string $password);

    public function getPlainPassword(): ?string;

    public function getPasswordResetHash(): ?string;

    public function setPasswordResetHash(?string $passwordResetHash);
}
