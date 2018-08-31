<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\RuleBundle\Form\Type\Rule\Condition\AbstractNestedConfigurationType;
use CoreShop\Bundle\ShippingBundle\Form\Type\ShippingRuleConditionCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class NestedConfigurationType extends AbstractNestedConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('conditions', ShippingRuleConditionCollectionType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $data = $event->getData();

            if (is_array($data)) {
                $data['conditions'] = [];

                $event->setData($data);
            }
        });
    }
}
