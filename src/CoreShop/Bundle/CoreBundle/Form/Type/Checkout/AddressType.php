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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class AddressType extends AbstractType
{
    /**
     * @var AddressFormatterInterface
     */
    private $addressFormatHelper;

    /**
     * @param AddressFormatterInterface $addressFormatHelper
     */
    public function __construct(AddressFormatterInterface $addressFormatHelper)
    {
        $this->addressFormatHelper = $addressFormatHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressChoiceType::class, [
                'constraints' => [new Valid()],
                'customer' => $options['customer'],
                'label' => 'coreshop.form.address.shipping',
                'choice_attr' => function ($val, $key, $index) {
                    if ($val instanceof AddressInterface) {
                        return [
                            'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($val)])
                        ];
                    }

                    return [];
                }
            ])
            ->add('invoiceAddress', AddressChoiceType::class, [
                'constraints' => [new Valid()],
                'customer' => $options['customer'],
                'label' => 'coreshop.form.address.invoice',
                'choice_attr' => function ($val, $key, $index) {
                    if ($val instanceof AddressInterface) {
                        return [
                            'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($val)])
                        ];
                    }

                    return [];
                }
            ])
            ->add('useShippingAsInvoice', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'data' => true,
                'label' => 'coreshop.form.address.use_shipping_as_invoice',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('customer');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_checkout_address';
    }
}
