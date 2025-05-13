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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\DefaultAddressAwareInterface;
use CoreShop\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use CoreShop\Component\User\Model\UserAwareInterface;

interface CustomerInterface extends BaseCustomerInterface, AddressesAwareInterface, UserAwareInterface, DefaultAddressAwareInterface
{
    public function getAddressAccessType(): ?string;

    public function setAddressAccessType(?string $addressAccessType);

    public function getNewsletterActive(): ?bool;

    public function setNewsletterActive(?bool $newsletterActive);

    public function getNewsletterConfirmed(): ?bool;

    public function setNewsletterConfirmed(?bool $newsletterConfirmed);

    public function getNewsletterToken(): ?string;

    public function setNewsletterToken(?string $newsletterToken);
}
