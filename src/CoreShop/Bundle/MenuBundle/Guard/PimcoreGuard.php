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

namespace CoreShop\Bundle\MenuBundle\Guard;

use Knp\Menu\ItemInterface;
use Pimcore\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PimcoreGuard
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function matchItem(ItemInterface $item): bool
    {
        if (!$item->getAttribute('permission')) {
            return true;
        }

        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        if ($user instanceof \Pimcore\Security\User\User) {
            $user = $user->getUser();
        }

        if ($user instanceof User) {
            return $user->isAllowed((string) $item->getAttribute('permission'));
        }

        return false;
    }
}
