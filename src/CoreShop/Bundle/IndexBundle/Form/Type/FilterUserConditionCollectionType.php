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

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\IndexBundle\Form\DataMapper\ConditionsFormMapper;
use CoreShop\Bundle\IndexBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterUserConditionCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('entry_type', FilterUserConditionType::class);
        $resolver->setDefault('nested', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['nested']) {
            $builder->setDataMapper(new ConditionsFormMapper($builder->getDataMapper()));
        }
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_filter_user_condition_collection';
    }
}
