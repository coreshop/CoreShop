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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

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
    public function __construct(
        private PimcoreRepositoryInterface $customerRepository,
        private CustomerAddressAllocatorInterface $customerAddressAllocator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('customer');
        $resolver
            ->setDefaults(
                [
                    'choices' => function (Options $options) {
                        /**
                         * @var CustomerInterface $customer
                         */
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
                ],
            )
        ;
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
