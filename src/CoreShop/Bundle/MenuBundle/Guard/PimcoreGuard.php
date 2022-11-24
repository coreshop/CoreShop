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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MenuBundle\Guard;

use Knp\Menu\ItemInterface;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\User;

class PimcoreGuard
{
    public function __construct(
        private TokenStorageUserResolver $tokenStorageUserResolver,
    ) {
    }

    public function matchItem(ItemInterface $item): bool
    {
        if (!$item->getAttribute('permission')) {
            return true;
        }

        $user = $this->tokenStorageUserResolver->getUser();

        if ($user instanceof User) {
            return $user->isAllowed((string) $item->getAttribute('permission'));
        }

        return false;
    }
}
