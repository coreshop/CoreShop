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

namespace CoreShop\Bundle\CoreBundle\Pimcore\Repository;

use CoreShop\Bundle\CustomerBundle\Pimcore\Repository\CustomerRepository as BaseCustomerRepository;
use CoreShop\Component\Core\Repository\CustomerRepositoryInterface;

class CustomerRepository extends BaseCustomerRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByEmailWithoutUser(string $email)
    {
        $list = $this->getList();

        $list->setCondition('email = ? AND user__id IS NULL', [$email]);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return null;
    }
}
