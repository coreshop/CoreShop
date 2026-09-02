<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle\Form\Type\Studio;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderShipmentCreationItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderItemId', HiddenType::class)
            ->add('maxQuantity', HiddenType::class, [
                'disabled' => true,
            ])
            ->add('name', TextType::class, [
                'disabled' => true,
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'disabled' => true,
                'required' => false,
            ])
            ->add('orderedQuantity', IntegerType::class, [
                'disabled' => true,
                'required' => false,
                'label' => 'coreshop_quantity',
            ])
            ->add('quantityShipped', IntegerType::class, [
                'disabled' => true,
                'required' => false,
                'label' => 'coreshop_shipped_quantity',
            ])
            ->add('quantity', IntegerType::class, [
                'required' => false,
                'label' => 'coreshop_to_ship',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new Callback(static function (mixed $data, ExecutionContextInterface $context): void {
                    if (!is_array($data)) {
                        return;
                    }

                    $quantity = $data['quantity'] ?? 0;
                    $maxQuantity = $data['maxQuantity'] ?? null;

                    if ($maxQuantity !== null && $quantity > (int) $maxQuantity) {
                        $context->buildViolation('Quantity ({{ quantity }}) exceeds the maximum shippable quantity ({{ max }}).')
                            ->setParameter('{{ quantity }}', (string) $quantity)
                            ->setParameter('{{ max }}', (string) $maxQuantity)
                            ->atPath('quantity')
                            ->addViolation();
                    }
                }),
            ],
        ]);
    }
}
