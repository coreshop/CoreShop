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

namespace CoreShop\Bundle\CoreBundle\Customer;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RegistrationService implements RegistrationServiceInterface
{
    /**
     * @var CustomerManagerInterface
     */
    private $customerManager;

    /**
     * @param CustomerManagerInterface $customerManager
     */
    public function __construct(CustomerManagerInterface $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomer(
        CustomerInterface $customer,
        AddressInterface $address,
        $formData,
        $isGuest = false
    ) {
        trigger_error(sprintf('Class %s has been deprecated with 2.1.0 in favor of %s and will be removed with 2.2.0', self::class, CustomerManagerInterface::class), E_USER_DEPRECATED);

        $customer->setAddresses([$address]);
        $this->customerManager->persistCustomer($customer);
    }
}
