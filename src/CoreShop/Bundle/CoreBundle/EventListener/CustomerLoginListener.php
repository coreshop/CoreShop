<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Kamil Wręczycki
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Customer\CustomerLoginServiceInterface;
use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;

final class CustomerLoginListener
{
    /**
     * @var CustomerLoginServiceInterface
     */
    private $customerLoginService;

    /**
     * @param CustomerLoginServiceInterface $customerLoginService
     */
    public function __construct(CustomerLoginServiceInterface $customerLoginService)
    {
        $this->customerLoginService = $customerLoginService;
    }

    /**
     * @param CustomerRegistrationEvent $customerRegistrationEvent
     */
    public function onCustomerRegister(CustomerRegistrationEvent $customerRegistrationEvent)
    {
        $this->customerLoginService->loginCustomer($customerRegistrationEvent->getCustomer());
    }
}
