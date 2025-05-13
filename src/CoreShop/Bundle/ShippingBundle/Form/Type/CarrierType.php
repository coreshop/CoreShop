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

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreAssetChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CarrierType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class)
            ->add('trackingUrl', TextType::class)
            ->add('hideFromCheckout', CheckboxType::class)
            ->add('logo', PimcoreAssetChoiceType::class)
            ->add('taxCalculationStrategy', ShippingTaxCalculationStrategyChoiceType::class)
            ->add('shippingRules', ShippingRuleGroupCollectionType::class)
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CarrierTranslationType::class,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_carrier';
    }
}
