<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'product'                   => null,
            'placeholder'               => false,
            'choices'                   => function (Options $options) {
                /** @var ProductInterface $product */
                $product = $options['product'];
                if ($product->hasUnitDefinitions() === false) {
                    return [];
                }

                return $product->getUnitDefinitions()->getUnitDefinitions();

            },
            'entry_type'                => ProductUnitDefinitionType::class,
            'choice_value'              => 'id',
            'choice_label'              => function (ProductUnitDefinitionInterface $definition) {
                return $definition->getUnit()->getName();
            },
            'choice_attr'               => function (ProductUnitDefinitionInterface $definition) {
                return ['data-precision-preset' => $definition->getPrecision()];
            },
            'choice_translation_domain' => false
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
        return 'coreshop_product_unit_definitions_choice';
    }
}
