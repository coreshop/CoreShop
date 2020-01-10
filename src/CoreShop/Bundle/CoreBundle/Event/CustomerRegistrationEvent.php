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

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerRegistrationEvent extends GenericEvent
{
    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var array
     */
    private $data;

    /**
     * @param CustomerInterface $customer
     * @param array             $data
     */
    public function __construct(CustomerInterface $customer, array $data)
    {
        parent::__construct($customer);

        $this->customer = $customer;
        $this->data = $data;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
