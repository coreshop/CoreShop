<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action;

use CoreShop\Bundle\OrderBundle\Form\Type\CartItemPriceRuleActionCollectionType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartItemPriceRuleConditionCollectionType;
use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
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
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('conditions', CartItemPriceRuleConditionCollectionType::class, [
                'constraints' => new Valid(['groups' => $this->conditionsValidationGroups]),
                'nested' => true,
            ]);
        $builder
            ->add('actions', CartItemPriceRuleActionCollectionType::class, [
                'constraints' => new Valid(['groups' => $this->actionsValidationGroups]),
                'nested' => true,
            ]);

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
