<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class IndexType extends AbstractResourceType
{
    /**
     * @var FormTypeRegistryInterface
     */
    private $formTypeRegistry;


    /**
     * {@inheritdoc}
     */
    public function __construct($dataClass, array $validationGroups = [], FormTypeRegistryInterface $formTypeRegistry)
    {
         parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'coreshop.form.index.name',
            ])
            ->add('worker', IndexWorkerChoiceType::class, [
                'label' => 'coreshop.form.index.worker',
            ])
            ->add('class', TextType::class, [ //TODO: Make this configurable using tags
                'label' => 'coreshop.form.index.class',
            ])
            ->add('columns', IndexColumnCollectionType::class, [
                'label' => 'coreshop.form.index.columns'
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($type, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                $event->getForm()->get('worker')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $data = $event->getData();

                if (!isset($data['worker'])) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['worker'], 'default'));
            })
        ;
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addConfigurationFields(FormInterface $form, $configurationType)
    {
        $form->add('configuration', $configurationType, [
            'label' => false,
        ]);
    }

    /**
     * @param FormInterface $form
     * @param mixed $data
     *
     * @return string|null
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null)
    {
        if (null !== $data && null !== $data->getWorker()) {
            return $data->getWorker();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_index';
    }
}
