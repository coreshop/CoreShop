<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

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
    public function __construct($dataClass, array $validationGroups = [], FormTypeRegistryInterface $formTypeRegistry, FormTypeRegistryInterface $getterTypeRegistry, FormTypeRegistryInterface $interpreterTypeRegistry)
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
            ->add('type', IndexColumnChoiceType::class)
            ->add('objectType', TextType::class)
            ->add('name', TextType::class)
            ->add('objectKey', TextType::class)
            ->add('columnType', TextType::class)
            ->add('getter', IndexColumnGetterChoiceType::class)
            ->add('interpreter', TextType::class)
            ->add('dataType', TextType::class)
        ;

        /**
         * Getter Configurations
         */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $data = $event->getData();

                if (!isset($data['getter'])) {
                    return;
                }

                $this->addGetterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($data['getter'], 'default'));
            })
        ;

        /**
         * Interpreter Configurations
         */
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $this->addInterpreterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getInterpreterRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('interpreter')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $data = $event->getData();

                if (!isset($data['interpreter'])) {
                    return;
                }

                $this->addInterpreterConfigurationFields($event->getForm(), $this->getterTypeRegistry->get($data['interpreter'], 'default'));
            })
        ;
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addGetterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('getterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addInterpreterConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('interpreterConfig', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed $data
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
     * @param mixed $data
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
