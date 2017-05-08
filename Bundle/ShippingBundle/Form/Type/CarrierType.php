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

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CarrierType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('label', TextType::class)
            ->add('trackingUrl', TextType::class)
            ->add('isFree', CheckboxType::class)
            ->add('rangeBehaviour', ChoiceType::class, [
                'choices' => [
                    'deactivate' => CarrierInterface::RANGE_BEHAVIOUR_DEACTIVATE,
                    'largest' => CarrierInterface::RANGE_BEHAVIOUR_LARGEST,
                ],
            ])
            ->add('shippingRules', ShippingRuleGroupCollectionType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_carrier';
    }
}
