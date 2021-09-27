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

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddressChoiceType extends AbstractType
{
    private PimcoreRepositoryInterface $customerRepository;
    private CustomerAddressAllocatorInterface $customerAddressAllocator;

    public function __construct(
        PimcoreRepositoryInterface $customerRepository,
        CustomerAddressAllocatorInterface $customerAddressAllocator
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerAddressAllocator = $customerAddressAllocator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('customer');
        $resolver
            ->setDefaults(
                [
                    'choices' => function (Options $options) {
                        $customer = $this->customerRepository->find($options['customer']);
                        $allowedAddressIdentifier = $options['allowed_address_identifier'];

                        $addresses = $this->customerAddressAllocator->allocateForCustomer($customer);

                        if (empty($allowedAddressIdentifier)) {
                            return $addresses;
                        }

                        return array_filter($addresses, static function (AddressInterface $address) use ($allowedAddressIdentifier): bool {
                            $addressIdentifierName = $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : null;

                            return in_array($addressIdentifierName, $allowedAddressIdentifier);
                        });
                    },
                    'choice_value' => 'id',
                    'choice_label' => function (AddressInterface $address) {
                        return sprintf('%s %s', $address->getStreet(), $address->getNumber());
                    },
                    'choice_translation_domain' => false,
                    'active' => true,
                    'allowed_address_identifier' => [],
                    'placeholder' => 'coreshop.form.address.choose_address',
                ]
            );
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_customer_address_choice';
    }
}
