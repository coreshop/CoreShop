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

use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierChoiceType;
use CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;

use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class CarrierType extends AbstractType
{
    /**
     * @var TaxedShippingCalculatorInterface
     */
    private $taxedShippingCalculator;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var TranslatorInterface
     */
    private $translator;


    public function __construct(
        TaxedShippingCalculatorInterface $taxedShippingCalculator,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter,
        CurrencyConverterInterface $currencyConverter,
        StoreContextInterface $storeContext,
        TranslatorInterface $translator
    ) {
        $this->taxedShippingCalculator = $taxedShippingCalculator;
        $this->translator = $translator;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter = $moneyFormatter;
        $this->currencyConverter = $currencyConverter;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cart = $options['cart'];

        $builder
            ->add('carrier', CarrierChoiceType::class, [
                'constraints' => [new Valid()],
                'compound'  => true,
                'expanded' => true,
                'label' => 'coreshop.ui.carrier',
                'choices' => $options['carriers'],
                'choice_value' => function ($carrier) {
                    return $carrier->getId();
                },
                'choice_label' => function ($carrier) use ($cart) {
                    $carrierPrice = $this->taxedShippingCalculator->getPrice($carrier, $cart, $cart->getShippingAddress());
                    $amount = $this->currencyConverter->convert($carrierPrice, $this->storeContext->getStore()->getCurrency()->getIsoCode(), $cart->getCurrency()->getIsoCode());
                    $formattedAmount = $this->moneyFormatter->format($amount, $this->currencyContext->getCurrency()->getIsoCode());
                    $label = 'coreshop.ui.carrier.' . strtolower(str_replace(' ', '_', $carrier->getLabel()));
                    return $this->translator->trans($label) . ' ' . $formattedAmount;
                }
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
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_checkout_carrier';
    }
}
