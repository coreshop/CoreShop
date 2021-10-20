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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Repository\CarrierRepositoryInterface;
use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface;
use CoreShop\Component\Shipping\Resolver\CarriersResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarrierChoiceType extends AbstractResourceType
{
    public function __construct(
        $dataClass,
        array $validationGroups,
        private CarrierRepositoryInterface $repository,
        private CarriersResolverInterface $carriersResolver,
        private TaxedShippingCalculatorInterface $taxedShippingCalculator,
        private CartContextResolverInterface $cartContextResolver
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $cart = $options['cart'];
                    if ($cart instanceof OrderInterface) {
                        return $this->carriersResolver->resolveCarriers($cart, $cart->getShippingAddress());
                    }

                    return $this->repository->findAll();
                },
                'choice_value' => 'id',
                'choice_label' => 'title',
                'choice_translation_domain' => false,
            ])
            ->setDefined([
                'cart',
            ])
            ->setDefault('show_carrier_price', true)
            ->setDefault('show_carrier_price_with_tax', true)
            ->setAllowedTypes('cart', OrderInterface::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $prices = [];
        /**
         * @var OrderInterface $cart
         */
        $cart = $options['cart'];

        foreach ($view->vars['choices'] as $choice) {
            $carrier = $choice->data;

            if (!$carrier instanceof CarrierInterface) {
                continue;
            }

            $price = $this->taxedShippingCalculator->getPrice(
                $carrier,
                $cart,
                $cart->getShippingAddress(),
                $options['show_carrier_price_with_tax'],
                $this->cartContextResolver->resolveCartContext($cart)
            );

            $prices[$choice->value] = $price;
        }

        $view->vars['prices'] = $prices;
        $view->vars['show_carrier_price'] = $options['show_carrier_price'];
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_checkout_carrier_choice';
    }
}
