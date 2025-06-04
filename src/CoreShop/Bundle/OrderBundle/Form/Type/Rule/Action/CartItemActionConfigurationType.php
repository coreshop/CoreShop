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

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action;

use CoreShop\Bundle\OrderBundle\Form\Type\CartItemPriceRuleActionCollectionType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartItemPriceRuleConditionCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;

final class CartItemActionConfigurationType extends AbstractType
{
    public function __construct(
        protected array $conditionsValidationGroups,
        protected array $actionsValidationGroups,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('conditions', CartItemPriceRuleConditionCollectionType::class, [
                'constraints' => new Valid(['groups' => $this->conditionsValidationGroups]),
                'nested' => true,
            ])
        ;
        $builder
            ->add('actions', CartItemPriceRuleActionCollectionType::class, [
                'constraints' => new Valid(['groups' => $this->actionsValidationGroups]),
                'nested' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (is_array($data)) {
                $data['conditions'] = [];
                $data['actions'] = [];

                $event->setData($data);
            }
        });
    }
}
