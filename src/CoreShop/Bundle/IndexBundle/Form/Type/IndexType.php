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
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreClassChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class IndexType extends AbstractResourceType
{
    private $formTypeRegistry;

    public function __construct(string $dataClass, array $validationGroups, FormTypeRegistryInterface $formTypeRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('worker', IndexWorkerChoiceType::class)
            ->add('class', PimcoreClassChoiceType::class)
            ->add('columns', IndexColumnCollectionType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());

                if (null === $type) {
                    return;
                }

                if (!$this->formTypeRegistry->has($type, 'default')) {
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['worker'])) {
                    return;
                }

                if (!$this->formTypeRegistry->has($data['worker'], 'default')) {
                    return;
                }

                $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($data['worker'], 'default'));
            });
    }

    /**
     * @param FormInterface $form
     * @param string        $configurationType
     */
    protected function addConfigurationFields(FormInterface $form, $configurationType): void
    {
        $form->add('configuration', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed         $data
     *
     * @return string|null
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
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
    public function getBlockPrefix(): string
    {
        return 'coreshop_index';
    }
}
