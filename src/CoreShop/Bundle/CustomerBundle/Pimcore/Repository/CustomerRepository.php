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

namespace CoreShop\Bundle\CustomerBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;

class CustomerRepository extends PimcoreRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByNewsletterToken($newsletterToken)
    {
        $list = $this->getList();
        $list->setCondition('newsletterToken = ?', [$newsletterToken]);
        $objects = $list->load();

        if (count($objects) === 1) {
            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByEmail($email)
    {
        $list = $this->getList();

        $list->setCondition('email = ?', [$email]);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0) {
            return $users[0];
        }

        return null;
    }
}
