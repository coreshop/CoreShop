<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ProductBundle\Form\Type\Unit;

use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductUnitDefinitionsChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'product' => null,
            'placeholder' => false,
            'choices' => function (Options $options) {
                /** @var ProductInterface $product */
                $product = $options['product'];
                if ($product->hasUnitDefinitions() === false) {
                    return [];
                }

                return $product->getUnitDefinitions()->getUnitDefinitions();
            },
            'entry_type' => ProductUnitDefinitionType::class,
            'choice_value' => 'id',
            'choice_label' => function (ProductUnitDefinitionInterface $definition) {
                return $definition->getUnit()->getFullLabel();
            },
            'choice_attr' => function (ProductUnitDefinitionInterface $definition) {
                return ['data-cs-unit-precision' => $definition->getPrecision()];
            },
            'choice_translation_domain' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_product_unit_definitions_choice';
    }
}
