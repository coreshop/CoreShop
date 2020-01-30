<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
    /**
     * @var FormTypeRegistryInterface
     */
    private $getterTypeRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    private $interpreterTypeRegistry;

    /**
     * {@inheritdoc}
     */
    public function __construct($dataClass, array $validationGroups, FormTypeRegistryInterface $formTypeRegistry, FormTypeRegistryInterface $getterTypeRegistry, FormTypeRegistryInterface $interpreterTypeRegistry)
    {
        parent::__construct($dataClass, $validationGroups, $formTypeRegistry);

        $this->getterTypeRegistry = $getterTypeRegistry;
        $this->interpreterTypeRegistry = $interpreterTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
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
            ->add('interpreter', IndexColumnInterpreterChoiceType::class);

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
            });

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
            });
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addGetterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('getterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     *
     * @return string|null
     */
    protected function getGetterRegistryIdentifier(FormInterface $form, $data = null)
    {
        if (null !== $data && null !== $data->getGetter()) {
            return $data->getGetter();
        }

        return null;
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     *
     * @return string|null
     */
    protected function getInterpreterRegistryIdentifier(FormInterface $form, $data = null)
    {
        if (null !== $data && null !== $data->getInterpreter()) {
            return $data->getInterpreter();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index_column';
    }
}
