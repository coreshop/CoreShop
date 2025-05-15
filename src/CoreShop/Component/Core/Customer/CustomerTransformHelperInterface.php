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

namespace CoreShop\Component\Core\Customer;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CompanyInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\Element\ElementInterface;

interface CustomerTransformHelperInterface
{
    public function getEntityAddressFolderPath(AddressInterface $address, string $rootPath): Folder;

    public function getSaveKeyForMoving(ElementInterface $object, ElementInterface $newParent): string;

    public function moveCustomerToNewCompany(CustomerInterface $customer, array $transformOptions): CustomerInterface;

    public function moveCustomerToExistingCompany(CustomerInterface $customer, CompanyInterface $company, array $transformOptions): CustomerInterface;

    public function moveAddressToNewAddressStack(AddressInterface $address, ElementInterface $newHolder, bool $removeOldRelations = true): AddressInterface;
}
