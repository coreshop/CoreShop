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

namespace CoreShop\Bundle\UserBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\User\Model\UserInterface;
use CoreShop\Component\User\Repository\UserRepositoryInterface;

class UserRepository extends PimcoreRepository implements UserRepositoryInterface
{
    public function findByResetToken(string $resetToken): ?UserInterface
    {
        $list = $this->getList();
        $list->setCondition('passwordResetHash = ?', [$resetToken]);
        $objects = $list->load();

        if (count($objects) === 1 && $objects[0] instanceof UserInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findByLoginIdentifier(string $value): ?UserInterface
    {
        $list = $this->getList();

        $conditions = ['loginIdentifier = ?'];
        $conditionsValues = [$value];

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0 && $users[0] instanceof UserInterface) {
            return $users[0];
        }

        return null;
    }
}
