<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle\Form\Type\Unit;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSelectionType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ProductUnitDefinitionsType extends AbstractResourceType
{
    /**
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($dataClass, array $validationGroups)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);

        $builder
            ->add('product', ProductSelectionType::class)
            ->add('defaultUnitDefinition', ProductUnitDefinitionType::class, [
                'mapped' => false
            ])
            ->add('additionalUnitDefinitions', ProductUnitDefinitionCollectionType::class, [
                'mapped' => false
            ]);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        /** @var ProductUnitDefinitionsInterface $unitDefinitions */
        $unitDefinitions = $event->getData();
        $form = $event->getForm();

        $defaultDefinition = $form->get('defaultUnitDefinition')->getData();
        if ($defaultDefinition) {
            $unitDefinitions->setDefaultUnitDefinition($defaultDefinition);
        }

        /** @var ProductUnitDefinitionInterface[] $additionalUnitDefinitions */
        $additionalUnitDefinitions = $form->get('additionalUnitDefinitions')->getData();
        foreach ($additionalUnitDefinitions as $key => $unitDefinition) {
            $existingDefinition = $unitDefinitions->getUnitDefinition($unitDefinition->getUnitName());
            if ($existingDefinition) {
                $unitDefinitions->addAdditionalUnitDefinition($unitDefinition);
                $additionalUnitDefinitions[$key] = $existingDefinition;
            }
        }

        // force collection to re-arrange unit definitions!
        PropertyAccess::createPropertyAccessor()->setValue($unitDefinitions, 'additionalUnitDefinitions', $additionalUnitDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_unit_definitions';
    }
}
