<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
