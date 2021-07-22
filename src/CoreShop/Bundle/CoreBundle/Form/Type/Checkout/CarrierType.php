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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierChoiceType;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

final class CarrierType extends AbstractResourceType
{
    /**
     * @var TaxedShippingCalculatorInterface
     */
    private $taxedShippingCalculator;

    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    public function __construct(
        $dataClass,
        array $validationGroups,
        ShopperContextInterface $shopperContext,
        TaxedShippingCalculatorInterface $taxedShippingCalculator,
        MoneyFormatterInterface $moneyFormatter,
        CurrencyConverterInterface $currencyConverter
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->taxedShippingCalculator = $taxedShippingCalculator;
        $this->shopperContext = $shopperContext;
        $this->moneyFormatter = $moneyFormatter;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cart = $options['cart'];

        $builder
            ->add('carrier', CarrierChoiceType::class, [
                'constraints' => [new Valid(), new NotBlank(['groups' => $this->validationGroups])],
                'expanded' => true,
                'label' => 'coreshop.ui.carrier',
                'choices' => $options['carriers'],
                'choice_value' => function ($carrier) {
                    if ($carrier instanceof CarrierInterface) {
                        return $carrier->getId();
                    }

                    return null;
                },
                'choice_label' => function ($carrier) use ($cart, $options) {
                    if (!$carrier instanceof CarrierInterface) {
                        return '';
                    }

                    if ($options['show_carrier_price'] === false) {
                        return $carrier->getTitle();
                    }

                    $carrierPrice = $this->taxedShippingCalculator->getPrice($carrier, $cart, $cart->getShippingAddress(), $options['show_carrier_price_with_tax']);
                    $amount = $this->currencyConverter->convert($carrierPrice, $this->shopperContext->getStore()->getCurrency()->getIsoCode(), $cart->getCurrency()->getIsoCode());
                    $formattedAmount = $this->moneyFormatter->format($amount, $this->shopperContext->getCurrency()->getIsoCode(), $this->shopperContext->getLocaleCode());

                    return sprintf('%s %s', $carrier->getTitle(), $formattedAmount);
                },
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'coreshop.ui.comment',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('carriers', null);
        $resolver->setDefault('cart', null);
        $resolver->setDefault('show_carrier_price', true);
        $resolver->setDefault('show_carrier_price_with_tax', true);
        $resolver->setAllowedTypes('cart', [CartInterface::class]);
        $resolver->setAllowedTypes('carriers', 'array')
            ->setAllowedValues('carriers', function (array $carriers) {
                // we already know it is an array as types are validated first
                foreach ($carriers as $carrier) {
                    if (!$carrier instanceof CarrierInterface) {
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_checkout_carrier';
    }
}
