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

namespace CoreShop\Bundle\StudioFormBundle\Form\Type\Demo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChoiceDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status (Select)',
                'choices' => [
                    'Draft' => 'draft',
                    'Published' => 'published',
                    'Archived' => 'archived',
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags (Multi-Select)',
                'choices' => [
                    'Featured' => 'featured',
                    'Sale' => 'sale',
                    'New' => 'new',
                    'Bestseller' => 'bestseller',
                ],
                'multiple' => true,
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Visibility (Radio)',
                'choices' => [
                    'Public' => 'public',
                    'Private' => 'private',
                    'Unlisted' => 'unlisted',
                ],
                'expanded' => true,
            ])
            ->add('channels', ChoiceType::class, [
                'label' => 'Channels (Checkbox Group)',
                'choices' => [
                    'Web' => 'web',
                    'Mobile' => 'mobile',
                    'POS' => 'pos',
                    'API' => 'api',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_choices';
    }
}
