<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Customer\Context;

use CoreShop\Component\Customer\Model\CustomerInterface;

final class FixedCustomerContext implements CustomerContextInterface
{
    /**
     * @var CustomerInterface
     */
    private $customer = null;

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        if ($this->customer instanceof CustomerInterface) {
            return $this->customer;
        }

        throw new CustomerNotFoundException();
    }
}
