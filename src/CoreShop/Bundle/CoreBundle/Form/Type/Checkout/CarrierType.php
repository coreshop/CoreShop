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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

final class CarrierType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrier', CarrierChoiceType::class, [
                'constraints' => [new Valid(), new NotBlank(['groups' => $this->validationGroups])],
                'expanded' => true,
                'label' => 'coreshop.ui.carrier',
                'cart' => $options['cart'],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'coreshop.ui.comment',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefined('cart');
        $resolver->setAllowedTypes('cart', OrderInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_checkout_carrier';
    }
}
