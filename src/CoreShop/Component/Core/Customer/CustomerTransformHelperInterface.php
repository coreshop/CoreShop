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

namespace CoreShop\Component\Core\Customer;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use Exception;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\ValidationException;

interface CustomerTransformHelperInterface
{
    public function getEntityAddressFolderPath(AddressInterface $address, string $rootPath): Folder;

    public function getSaveKeyForMoving(ElementInterface $object, ElementInterface $newParent): string;

    public function moveCustomerToNewCompany(CustomerInterface $customer, array $transformOptions): CustomerInterface;

    public function moveCustomerToExistingCompany(CustomerInterface $customer, CompanyInterface $company, array $transformOptions): CustomerInterface;

    public function moveAddressToNewAddressStack(AddressInterface $address, ElementInterface $newHolder, bool $removeOldRelations = true): AddressInterface;
}
