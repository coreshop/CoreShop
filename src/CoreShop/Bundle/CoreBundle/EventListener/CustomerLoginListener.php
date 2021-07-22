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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Customer\CustomerLoginServiceInterface;
use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;

final class CustomerLoginListener
{
    private CustomerLoginServiceInterface $customerLoginService;

    public function __construct(CustomerLoginServiceInterface $customerLoginService)
    {
        $this->customerLoginService = $customerLoginService;
    }

    public function onCustomerRegister(CustomerRegistrationEvent $customerRegistrationEvent): void
    {
        if (null !== $customerRegistrationEvent->getCustomer()->getUser()) {
            $this->customerLoginService->loginCustomer($customerRegistrationEvent->getCustomer()->getUser());
        }
    }
}
