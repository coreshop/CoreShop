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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\OrderBundle\Form\Type\CartItemType;
use CoreShop\Bundle\OrderBundle\Form\Type\QuantityType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionsChoiceType;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartItemTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['allow_units']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$data instanceof OrderItemInterface) {
                return;
            }

            /** @var ProductInterface $product */
            $product = $data->getProduct();
            if (!$product instanceof ProductInterface) {
                return;
            }

            $form
                ->remove('quantity')
                ->add('quantity', QuantityType::class, [
                    'html5' => true,
                    'unit_definition' => $data->hasUnitDefinition() ? $data->getUnitDefinition() : null,
                    'label' => 'coreshop.ui.quantity',
                    'disabled' => (bool)$data->getIsGiftItem(),
                ]);

            if (!$product->hasUnitDefinitions()) {
                return;
            }

            $form->add('unitDefinition', ProductUnitDefinitionsChoiceType::class, [
                'product' => $product,
                'required' => false,
                'label' => null,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('allow_units', false);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }
}
