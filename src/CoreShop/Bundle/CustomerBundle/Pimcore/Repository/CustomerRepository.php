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

namespace CoreShop\Bundle\CustomerBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;

class CustomerRepository extends PimcoreRepository implements CustomerRepositoryInterface
{
    public function findOneByEmail($email)
    {
        $list = $this->getList();

        $list->setCondition('email = ?', [$email]);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0 && $users[0] instanceof CustomerInterface) {
            return $users[0];
        }

        return null;
    }

    public function findByNewsletterToken(string $newsletterToken): ?CustomerInterface
    {
        $list = $this->getList();
        $list->setCondition('newsletterToken = ?', [$newsletterToken]);
        $objects = $list->load();

        if (count($objects) === 1 && $objects[0] instanceof CustomerInterface) {
            return $objects[0];
        }

        return null;
    }

    public function findUniqueByEmail(string $email, bool $isGuest): ?CustomerInterface
    {
        $list = $this->getList();

        $conditions = ['email = ?'];
        $conditionsValues = [$email];

        if (!$isGuest) {
            $conditions[] = 'user__id IS NOT NULL';
        } else {
            $conditions[] = 'user__id IS NULL';
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0 && $users[0] instanceof CustomerInterface) {
            return $users[0];
        }

        return null;
    }

    public function findGuestByEmail(string $email): ?CustomerInterface
    {
        return $this->findUniqueByEmail($email, true);
    }

    public function findCustomerByEmail(string $email): ?CustomerInterface
    {
        return $this->findUniqueByEmail($email, false);
    }
}
