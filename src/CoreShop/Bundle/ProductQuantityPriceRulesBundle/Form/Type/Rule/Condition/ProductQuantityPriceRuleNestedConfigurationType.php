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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\ProductQuantityPriceRuleConditionCollectionType;
use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductQuantityPriceRuleNestedConfigurationType extends AbstractNestedConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('conditions', ProductQuantityPriceRuleConditionCollectionType::class, ['nested' => true]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (is_array($data)) {
                $data['conditions'] = [];

                $event->setData($data);
            }
        });
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_product_quantity_price_rules_rule_condition_nested';
    }
}
