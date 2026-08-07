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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderShipmentCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('trackingCode', TextType::class, [
                'required' => false,
                'label' => 'coreshop_tracking_code',
                'priority' => 300,
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => OrderShipmentCreationItemsType::class,
                'block_prefix' => 'grid_collection',
                'label' => 'coreshop_products',
                'priority' => 200,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_order_shipment_creation';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
