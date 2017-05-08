<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

namespace CoreShop\Component\Customer\Repository;

use Pimcore\Model\Object\Listing;

/**
 * Class CustomerRepository.
 */
class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var string
     */
    private $customerListClass;

    /**
     * @param string $customerListClass
     */
    public function __construct($customerListClass)
    {
        $this->customerListClass = $customerListClass;
    }

    /**
     * @return Listing
     */
    private function createList()
    {
        return new $this->customerListClass();
    }

    /**
     * Find Customer by email.
     *
     * @param $email
     * @param $isGuest
     *
     * @return mixed
     */
    public function getUniqueByEmail($email, $isGuest)
    {
        $list = $this->createList();

        $conditions = ['email = ?'];
        $conditionsValues = [$email];
        $conditionsValues[] = $isGuest ? 1 : 0;

        if (!$isGuest) {
            $conditions[] = '(isGuest = ? OR isGuest IS NULL)';
        } else {
            $conditions[] = 'isGuest = ?';
        }

        $list->setCondition(implode(' AND ', $conditions), $conditionsValues);

        $users = $list->load();

        if (count($users) > 0) {
            return $users[0];
        }

        return false;
    }

    /**
     * Find Guest Customer by Email.
     *
     * @param $email
     *
     * @return mixed
     */
    public function getGuestByEmail($email)
    {
        return $this->$this->getUniqueByEmail($email, true);
    }

    /**
     * Find Customer by Email.
     *
     * @param $email
     *
     * @return mixed
     */
    public function getCustomerByEmail($email)
    {
        return $this->$this->getUniqueByEmail($email, false);
    }
}
