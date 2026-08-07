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

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarrierChoiceType extends AbstractType
{
    public function __construct(
        private RepositoryInterface $carrierRepository,
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
                    /**
                     * @var CarrierInterface[] $carriers
                     */
                    $carriers = $this->carrierRepository->findAll();

                    usort($carriers, function (CarrierInterface $a, CarrierInterface $b): int {
                        return $a->getIdentifier() <=> $b->getIdentifier();
                    });

                    return $carriers;
                },
                'choice_value' => 'id',
                'choice_label' => 'identifier',
                'choice_translation_domain' => false,
                'active' => true,
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $description = [];
        $carriers = $form->getConfig()->getOption('choices');
        foreach ($carriers as $carrier) {
            if (!empty($carrier->getDescription())) {
                $description[$carrier->getId()] = $carrier->getDescription();
            }
        }
        $view->vars = array_merge($view->vars, [
            'choices_description' => $description,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_carrier_choice';
    }
}
