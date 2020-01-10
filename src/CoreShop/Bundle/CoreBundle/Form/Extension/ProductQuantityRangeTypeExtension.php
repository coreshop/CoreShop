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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionSelectionType;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\ProductQuantityRangeType;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ProductQuantityRangeTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, [])
            ->add('currency', CurrencyChoiceType::class, [])
            ->add('percentage', NumberType::class, [])
            ->add('pseudoPrice', MoneyType::class, [])
            ->add('unitDefinition', ProductUnitDefinitionSelectionType::class, []);

        if ($builder->has('rangeStartingFrom')) {
            $builder->get('rangeStartingFrom')->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'roundQuantity'], -2048);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function roundQuantity(FormEvent $event)
    {
        $form = $event->getForm();
        $parentForm = $form->getParent();

        $scale = $this->getScale($parentForm);
        if ($scale === null) {
            return;
        }

        $quantity = (float) str_replace(',', '.', $event->getData());
        $formattedQuantity = round($quantity, $scale, PHP_ROUND_HALF_UP);

        if ($quantity !== $formattedQuantity) {
            $event->setData((string) $formattedQuantity);
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return int|null
     */
    protected function getScale(FormInterface $form)
    {
        if (!$form->has('unitDefinition')) {
            return null;
        }

        $productUnitDefinition = $form->get('unitDefinition')->getData();
        if (!$productUnitDefinition instanceof ProductUnitDefinitionInterface) {
            return null;
        }

        $precision = $productUnitDefinition->getPrecision();

        if (is_int($precision)) {
            return $precision;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductQuantityRangeType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [ProductQuantityRangeType::class];
    }
}
