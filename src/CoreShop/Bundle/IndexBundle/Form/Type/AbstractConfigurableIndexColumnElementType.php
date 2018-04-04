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

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurableIndexColumnElementType extends AbstractResourceType
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
        parent::buildForm($builder, $options);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $objectType = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $objectType) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($objectType, 'default'));
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $objectType = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $objectType) {
                    return;
                }

                $event->getForm()->get('objectType')->setData($objectType);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($options) {
                $data = $event->getData();

                if (!isset($data['objectType'])) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['objectType'], 'default'));
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('configuration_type', null)
            ->setAllowedTypes('configuration_type', ['string', 'null']);
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
        if (null !== $data && null !== $data->getObjectType()) {
            return $data->getObjectType();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }
}
