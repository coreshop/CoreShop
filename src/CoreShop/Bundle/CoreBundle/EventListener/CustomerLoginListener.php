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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Customer\CustomerLoginServiceInterface;
use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\UserInterface;

final class CustomerLoginListener
{
    public function __construct(private CustomerLoginServiceInterface $customerLoginService)
    {
    }

    public function onCustomerRegister(CustomerRegistrationEvent $customerRegistrationEvent): void
    {
        $user = $customerRegistrationEvent->getCustomer()->getUser();
        if ($user instanceof UserInterface) {
            $this->customerLoginService->loginCustomer($user);
        }
    }
}
