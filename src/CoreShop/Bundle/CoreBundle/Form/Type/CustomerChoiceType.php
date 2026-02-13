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

final class CustomerChoiceType extends AbstractType
{
    public function __construct(
        private RepositoryInterface $customerRepository,
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
                    $customers = $this->customerRepository->findAll();

                    usort($customers, function (object $a, object $b): int {
                        $labelA = $this->getCustomerLabel($a);
                        $labelB = $this->getCustomerLabel($b);

                        return $labelA <=> $labelB;
                    });

                    return $customers;
                },
                'choice_value' => 'id',
                'choice_label' => function (object $entity): string {
                    return $this->getCustomerLabel($entity);
                },
                'choice_translation_domain' => false,
            ])
        ;
    }

    private function getCustomerLabel(object $entity): string
    {
        $parts = [];

        if (method_exists($entity, 'getFirstname') && $entity->getFirstname()) {
            $parts[] = $entity->getFirstname();
        }

        if (method_exists($entity, 'getLastname') && $entity->getLastname()) {
            $parts[] = $entity->getLastname();
        }

        if (!empty($parts)) {
            $name = implode(' ', $parts);

            if (method_exists($entity, 'getEmail') && $entity->getEmail()) {
                return $name . ' (' . $entity->getEmail() . ')';
            }

            return $name;
        }

        if (method_exists($entity, 'getEmail') && $entity->getEmail()) {
            return $entity->getEmail();
        }

        return '#' . $entity->getId();
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_customer_choice';
    }
}
