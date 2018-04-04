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
 */

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface, AddressesAwareInterface
{
    /**
     * @return AddressInterface
     */
    public function getDefaultAddress();

    /**
     * @param AddressInterface $address
     */
    public function setDefaultAddress($address);

    /**
     * @return boolean
     */
    public function getNewsletterActive();

    /**
     * @param boolean $newsletterActive
     */
    public function setNewsletterActive($newsletterActive);

    /**
     * @return boolean
     */
    public function getNewsletterConfirmed();

    /**
     * @param boolean $newsletterConfirmed
     */
    public function setNewsletterConfirmed($newsletterConfirmed);

    /**
     * @return string
     */
    public function getNewsletterToken();

    /**
     * @param string $newsletterToken
     */
    public function setNewsletterToken($newsletterToken);
}
