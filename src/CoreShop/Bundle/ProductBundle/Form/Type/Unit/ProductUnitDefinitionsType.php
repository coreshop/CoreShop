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
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);

        $builder
            ->add('product', ProductSelectionType::class)
            ->add('defaultUnitDefinition', ProductUnitDefinitionType::class)
            ->add('additionalUnitDefinitions', ProductUnitDefinitionCollectionType::class);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $event->setData($this->parseStorePostData($data));
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
            }
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function parseStorePostData(array $data)
    {
        $objectId = $data['objectId'];

        $defaultUnitDefinition = null;

        if (is_array($data['defaultUnitDefinition'])) {
            $defaultUnitDefinition = $data['defaultUnitDefinition'];
        }

        $additionalUnitDefinitions = [];
        if (is_array($data['additionalUnitDefinitions'])) {
            foreach ($data['additionalUnitDefinitions'] as $additionalUnitDefinition) {
                $additionalUnitDefinitions[] = $additionalUnitDefinition;
            }
        }

        return [
            'product'                   => $objectId,
            'defaultUnitDefinition'     => $defaultUnitDefinition,
            'additionalUnitDefinitions' => $additionalUnitDefinitions
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_unit_definitions';
    }
}
