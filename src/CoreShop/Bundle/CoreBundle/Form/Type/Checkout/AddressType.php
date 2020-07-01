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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AddressType extends AbstractResourceType
{
    /**
     * @var AddressFormatterInterface
     */
    private $addressFormatHelper;

    /**
     * @param string                    $dataClass
     * @param string[]                  $validationGroups
     * @param AddressFormatterInterface $addressFormatHelper
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        AddressFormatterInterface $addressFormatHelper
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->addressFormatHelper = $addressFormatHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultShippingAddress = null;
        $defaultInvoiceAddress = null;

        if ($options['customer']->getDefaultAddress() instanceof AddressInterface) {
            /** @var AddressInterface $address */
            $address = $options['customer']->getDefaultAddress();

            if (!$address->hasAddressIdentifier()) {
                $defaultShippingAddress = $address;
                $defaultInvoiceAddress = $address;
            } else {
                $addressIdentifier = $address->getAddressIdentifier();
                $defaultShippingAddress = $addressIdentifier->getName() === 'shipping' ? $address : null;
                $defaultInvoiceAddress = $addressIdentifier->getName() === 'invoice' ? $address : null;
            }
        }

        $builder
            ->add('shippingAddress', AddressChoiceType::class, [
                'constraints' => [new NotBlank(['groups' => $this->validationGroups])],
                'customer' => $options['customer']->getId(),
                'label' => 'coreshop.form.address.shipping',
                'allowed_address_identifier' => [null, 'shipping'],
                'choice_attr' => function ($address) {
                    if ($address instanceof AddressInterface) {
                        return [
                            'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($address)]),
                            'data-address-type' => $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : '',
                        ];
                    }

                    return [];
                },
                'empty_data' => $defaultShippingAddress,
            ])
            ->add('invoiceAddress', AddressChoiceType::class, [
                'constraints' => [new NotBlank(['groups' => $this->validationGroups])],
                'customer' => $options['customer']->getId(),
                'label' => 'coreshop.form.address.invoice',
                'allowed_address_identifier' => [null, 'invoice'],
                'choice_attr' => function ($address) {
                    if ($address instanceof AddressInterface) {
                        return [
                            'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($address)]),
                            'data-address-type' => $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : '',
                        ];
                    }

                    return [];
                },
                'empty_data' => $defaultInvoiceAddress,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var CartInterface $cart */
                $cart = $event->getData();
                $checkboxData = true;
                $checkboxDisabled = false;

                if ($event->getForm()->has('shippingAddress') && $event->getForm()->get('shippingAddress')->getConfig()->hasOption('choices')) {
                    $choiceList = $event->getForm()->get('shippingAddress')->getConfig()->getOption('choices');
                    if (!is_array($choiceList) || count($choiceList) === 0) {
                        $checkboxData = null;
                        $checkboxDisabled = true;
                    }
                }

                if ($cart->getShippingAddress() instanceof AddressInterface && $cart->getInvoiceAddress() instanceof AddressInterface) {
                    if ($cart->getShippingAddress()->getId() !== $cart->getInvoiceAddress()->getId()) {
                        $checkboxData = null;
                    }
                }

                $event->getForm()->add('useInvoiceAsShipping', CheckboxType::class, [
                    'required' => false,
                    'mapped' => false,
                    'disabled' => $checkboxDisabled,
                    'label' => 'coreshop.form.address.use_invoice_as_shipping',
                    'data' => $checkboxData,
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $formData = $event->getData();
                if (isset($formData['invoiceAddress']) && (isset($formData['useInvoiceAsShipping']) && '1' === $formData['useInvoiceAsShipping'])) {
                    $formData['shippingAddress'] = $formData['invoiceAddress'];
                    $event->setData($formData);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('customer');
        $resolver->setAllowedTypes('customer', [CustomerInterface::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_checkout_address';
    }
}
