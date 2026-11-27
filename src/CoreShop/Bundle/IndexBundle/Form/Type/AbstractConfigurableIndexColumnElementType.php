<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurableIndexColumnElementType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private FormTypeRegistryInterface $formTypeRegistry,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $objectType = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $objectType) {
                    return;
                }

                if (!$this->formTypeRegistry->has($objectType, 'default')) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($objectType, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $objectType = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $objectType) {
                    return;
                }

                $event->getForm()->get('objectType')->setData($objectType);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $data = $event->getData();

                // The form may have been built for another column: entries of the columns collection
                // are matched by position, so after a column was removed (or its type changed) the
                // submitted column meets the configuration form of the one stored at its position.
                // Without a configuration of its own, that form must go, or it validates and maps
                // the other column's configuration.
                if (!isset($data['objectType']) || !$this->formTypeRegistry->has($data['objectType'], 'default')) {
                    $event->getForm()->remove('configuration');

                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['objectType'], 'default'));
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                $column = $event->getData();

                // No configuration form means no configuration: drop what the stored column had.
                if ($column instanceof IndexColumnInterface && !$event->getForm()->has('configuration')) {
                    $column->setConfiguration([]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null'])
        ;
    }

    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('configuration', $configurationType, [
            'label' => false,
        ]);
    }

    /**
     * @param mixed         $data
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getObjectType()) {
            return $data->getObjectType();
        }

        if ($form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }
}
