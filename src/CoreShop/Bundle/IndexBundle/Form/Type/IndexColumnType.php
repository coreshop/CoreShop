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

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class IndexColumnType extends AbstractConfigurableIndexColumnElementType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        FormTypeRegistryInterface $formTypeRegistry,
        private FormTypeRegistryInterface $getterTypeRegistry,
        private FormTypeRegistryInterface $interpreterTypeRegistry,
    ) {
        parent::__construct($dataClass, $validationGroups, $formTypeRegistry);
    }

    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('objectType', IndexColumnChoiceType::class)
            ->add('dataType', TextType::class)
            ->add('name', TextType::class)
            ->add('objectKey', TextType::class)
            ->add('columnType', TextType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('getter', IndexColumnGetterChoiceType::class)
            ->add('interpreter', IndexColumnInterpreterChoiceType::class)
        ;

        /*
         * Getter Configurations
         */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getGetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $this->addGetterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getGetterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('getter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['getter'])) {
                    return;
                }

                $this->addGetterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($data['getter'], 'default'));
            })
        ;

        /*
         * Interpreter Configurations
         */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $this->addInterpreterConfigurationFields($event->getForm(), $this->interpreterTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('interpreter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['interpreter'])) {
                    return;
                }

                $this->addInterpreterConfigurationFields($event->getForm(), $this->interpreterTypeRegistry->get($data['interpreter'], 'default'));
            })
        ;
    }

    /**
     * @param string        $configurationType
     */
    protected function addGetterConfigurationFields(FormInterface $form, $configurationType): void
    {
        $form->add('getterConfig', $configurationType);
    }

    /**
     * @param string        $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, $configurationType): void
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param mixed         $data
     */
    protected function getGetterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getGetter()) {
            return $data->getGetter();
        }

        return null;
    }

    /**
     * @param mixed         $data
     */
    protected function getInterpreterRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getInterpreter()) {
            return $data->getInterpreter();
        }

        return null;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_index_column';
    }
}
