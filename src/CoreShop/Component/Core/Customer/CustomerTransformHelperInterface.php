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

namespace CoreShop\Component\Core\Customer;

use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use Exception;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\ValidationException;

interface CustomerTransformHelperInterface
{
    /**
     * @param string $rootPath
     *
     * @return Concrete
     */
    public function getEntityAddressFolderPath(string $rootPath);

    /**
     * @param ElementInterface $object
     * @param ElementInterface $newParent
     *
     * @return string
     */
    public function getSaveKeyForMoving(ElementInterface $object, ElementInterface $newParent);

    /**
     * @param CustomerInterface $customer
     * @param array             $transformOptions
     *
     * @return CustomerInterface
     * @throws Exception
     * @throws ValidationException
     */
    public function moveCustomerToNewCompany(CustomerInterface $customer, array $transformOptions);

    /**
     * @param CustomerInterface $customer
     * @param CompanyInterface  $company
     * @param array             $transformOptions
     *
     * @return CustomerInterface
     * @throws Exception
     * @throws ValidationException
     */
    public function moveCustomerToExistingCompany(CustomerInterface $customer, CompanyInterface $company, array $transformOptions);
}
