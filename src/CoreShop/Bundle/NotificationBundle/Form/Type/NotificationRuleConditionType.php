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

namespace CoreShop\Bundle\NotificationBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleConditionType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotificationRuleConditionType extends RuleConditionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', NotificationRuleConditionChoiceType::class, [
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_notification_rule_condition';
    }
}
