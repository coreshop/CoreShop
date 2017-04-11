<?php

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\ShippingBundle\Form\Type\CarrierType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRuleGroupChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CarrierTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('taxRule', TaxRuleGroupChoiceType::class);
        $builder->add('stores', StoreChoiceType::class, [
                'multiple' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CarrierType::class;
    }
}
