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

namespace CoreShop\Bundle\CustomerBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;

class CustomerRepository extends PimcoreRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByResetToken($resetToken)
    {
        $list = $this->getList();
        $list->setCondition('passwordResetHash = ?', [$resetToken]);
        $objects = $list->load();

        if (count($objects) === 1 && $objects[0] instanceof CustomerInterface) {
            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByNewsletterToken($newsletterToken)
    {
        $list = $this->getList();
        $list->setCondition('newsletterToken = ?', [$newsletterToken]);
        $objects = $list->load();

        if (count($objects) === 1 && $objects[0] instanceof CustomerInterface) {
            return $objects[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findUniqueByLoginIdentifier(string $identifier, $value, $isGuest)
    {
        $list = $this->getList();

        $conditions = [sprintf('%s = ?', $identifier)];
        $conditionsValues = [$value];
        $conditionsValues[] = $isGuest ? 1 : 0;

        if (!$isGuest) {
            $conditions[] = '(isGuest = ? OR isGuest IS NULL)';
        } else {
            $conditions[] = 'isGuest = ?';
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);
        $list->load();

        $users = $list->getObjects();

        if (count($users) > 0 && $users[0] instanceof CustomerInterface) {
            return $users[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findUniqueByEmail($email, $isGuest)
    {
        return $this->findUniqueByLoginIdentifier('email', $email, $isGuest);
    }

    /**
     * {@inheritdoc}
     */
    public function findUniqueByUsername($username, $isGuest)
    {
        return $this->findUniqueByLoginIdentifier('username', $username, $isGuest);
    }

    /**
     * {@inheritdoc}
     */
    public function findGuestByEmail($email)
    {
        return $this->findUniqueByEmail($email, true);
    }

    /**
     * {@inheritdoc}
     */
    public function findCustomerByEmail($email)
    {
        return $this->findUniqueByEmail($email, false);
    }

    /**
     * {@inheritdoc}
     */
    public function findCustomerByUsername($username)
    {
        return $this->findUniqueByUsername($username, false);
    }
}
