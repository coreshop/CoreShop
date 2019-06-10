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

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Product\Model\ProductUnitDefinitionPriceInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductUnitDefinitionPriceType extends AbstractResourceType
{
    /**
     * @var int
     */
    protected $decimalFactor;

    /**
     * @var int
     */
    protected $decimalPrecision;

    /**
     * @param       $dataClass
     * @param array $validationGroups
     * @param int   $decimalFactor
     * @param int   $decimalPrecision
     */
    public function __construct($dataClass, array $validationGroups, int $decimalFactor, int $decimalPrecision)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

        $builder
            ->add('price', IntegerType::class)
            ->add('unitDefinition', ProductUnitDefinitionSelectionType::class);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        /** @var ProductUnitDefinitionPriceInterface $data */
        $data = $event->getData();

        if (!is_numeric($data['price'])) {
            $data['price'] = 0;
        } else {
            $data['price'] = (int) round((round($data['price'], $this->decimalPrecision) * $this->decimalFactor), 0);
        }

        $event->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_unit_definition_price';
    }
}
