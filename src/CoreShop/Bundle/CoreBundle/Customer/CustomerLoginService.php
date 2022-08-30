<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Component\Core\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class CustomerLoginService implements CustomerLoginServiceInterface
{
    public function __construct(private TokenStorageInterface $securityTokenStorage)
    {
    }

    public function loginCustomer(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, null, 'coreshop_frontend', $user->getRoles());
        $this->securityTokenStorage->setToken($token);
    }
}
