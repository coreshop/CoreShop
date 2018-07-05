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

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
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
     * @param PimcoreRepositoryInterface $customerRepository
     */
    public function __construct(PimcoreRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('customer');
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $customer = $this->customerRepository->find($options['customer']);

                    if (!$customer instanceof CustomerInterface) {
                        throw new \InvalidArgumentException('Customer needs to be set');
                    }

                    return $customer->getAddresses();
                },
                'choice_value' => 'o_id',
                'choice_label' => function ($value, $key, $index) {
                    if ($value instanceof AddressInterface) {
                        return sprintf('%s %s', $value->getStreet(), $value->getNumber());
                    }

                    return null;
                },
                'choice_translation_domain' => false,
                'active' => true,
                'placeholder' => 'coreshop.form.address.choose_address',
            ]);
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
