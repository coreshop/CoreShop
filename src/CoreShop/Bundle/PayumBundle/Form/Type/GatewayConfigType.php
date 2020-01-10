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

namespace CoreShop\Bundle\PayumBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Payum\Core\Model\GatewayConfigInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class GatewayConfigType extends AbstractResourceType
{
    /**
     * @var FormTypeRegistryInterface
     */
    private $gatewayConfigurationTypeRegistry;

    /**
     * {@inheritdoc}
     *
     * @param FormTypeRegistryInterface $gatewayConfigurationTypeRegistry
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        FormTypeRegistryInterface $gatewayConfigurationTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->gatewayConfigurationTypeRegistry = $gatewayConfigurationTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('factoryName', TextType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$gatewayConfig instanceof GatewayConfigInterface) {
                    return;
                }

                if (!$this->gatewayConfigurationTypeRegistry->has('gateway_config', $gatewayConfig->getFactoryName())) {
                    return;
                }

                $configType = $this->gatewayConfigurationTypeRegistry->get('gateway_config', $gatewayConfig->getFactoryName());

                $event->getForm()->add('config', $configType, [
                    'auto_initialize' => false,
                ]);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_payum_gateway_config';
    }
}
