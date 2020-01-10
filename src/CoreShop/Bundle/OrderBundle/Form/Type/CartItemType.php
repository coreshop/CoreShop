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

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Core\Model\CartItemInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CartItemType extends AbstractResourceType
{
    /**
     * @var DataMapperInterface
     */
    private $dataMapper;

    /**
     * @param string              $dataClass
     * @param array               $validationGroups
     * @param DataMapperInterface $dataMapper
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        DataMapperInterface $dataMapper
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->dataMapper = $dataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data instanceof CartItemInterface) {
                return;
            }

            $event->getForm()->add('quantity', QuantityType::class, [
                'html5' => true,
                'unit_definition' => $data->hasUnitDefinition() ? $data->getUnitDefinition() : null,
                'label' => 'coreshop.ui.quantity',
                'disabled' => $data->getIsGiftItem(),
            ]);
        });

        $builder->setDataMapper($this->dataMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_item';
    }
}
