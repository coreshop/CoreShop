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
    /**
     * @var PimcoreRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerAddressAllocatorInterface
     */
    private $customerAddressAllocator;

    /**
     * @param PimcoreRepositoryInterface $customerRepository
     * @param CustomerAddressAllocatorInterface $customerAddressAllocator
     */
    public function __construct(PimcoreRepositoryInterface $customerRepository, CustomerAddressAllocatorInterface $customerAddressAllocator)
    {
        $this->customerRepository = $customerRepository;
        $this->customerAddressAllocator = $customerAddressAllocator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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

                        if (!$customer instanceof CustomerInterface) {
                            throw new \InvalidArgumentException('Customer needs to be set');
                        }

                        $addresses = $this->customerAddressAllocator->allocateForCustomer($customer);

                        if (empty($allowedAddressIdentifier)) {
                            return $addresses;
                        }

                        return array_filter($addresses, function (AddressInterface $address) use ($allowedAddressIdentifier) {
                            $addressIdentifierName = $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : null;

                            return in_array($addressIdentifierName, $allowedAddressIdentifier);
                        });
                    },
                    'choice_value' => 'id',
                    'choice_label' => function ($address) {
                        if ($address instanceof AddressInterface) {
                            return sprintf('%s %s', $address->getStreet(), $address->getNumber());
                        }

                        return null;
                    },
                    'choice_translation_domain' => false,
                    'active' => true,
                    'allowed_address_identifier' => [],
                    'placeholder' => 'coreshop.form.address.choose_address',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_customer_address_choice';
    }
}
