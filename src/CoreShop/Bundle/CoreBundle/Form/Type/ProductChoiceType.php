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

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductChoiceType extends AbstractType
{
    public function __construct(
        private RepositoryInterface $productRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $products = $this->productRepository->findAll();

                    usort($products, function (object $a, object $b): int {
                        return ($a->getName() ?? '') <=> ($b->getName() ?? '');
                    });

                    return $products;
                },
                'choice_value' => 'id',
                'choice_label' => function (object $entity): string {
                    if (method_exists($entity, 'getName') && $entity->getName()) {
                        return $entity->getName();
                    }

                    if (method_exists($entity, 'getKey') && $entity->getKey()) {
                        return $entity->getKey();
                    }

                    return '#' . $entity->getId();
                },
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_product_choice';
    }
}
