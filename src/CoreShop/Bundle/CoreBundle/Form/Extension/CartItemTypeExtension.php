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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\OrderBundle\Form\Type\CartItemType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionsChoiceType;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartItemTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['allow_units']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data instanceof CartItemInterface) {
                return;
            }

            /** @var ProductInterface $product */
            $product = $data->getProduct();
            if (!$product instanceof ProductInterface) {
                return;
            }

            if (!$product->hasUnitDefinitions()) {
                return;
            }

            $event->getForm()->add('unitDefinition', ProductUnitDefinitionsChoiceType::class, [
                'product'  => $product,
                'required' => false,
                'label'    => null,
            ]);
        });

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'roundQuantity'], -2048)
            ->addEventListener(FormEvents::POST_SET_DATA, [$this, 'roundQuantity'], -2048)
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'roundQuantity'], -2048)
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'roundQuantity'], -2048);
    }

    /**
     * @param FormEvent $event
     */
    public function roundQuantity(FormEvent $event)
    {
        $data = $event->getData();
        if (!$data instanceof CartItemInterface) {
            return;
        }

        $product = $data->getProduct();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $scale = $this->getScale($event->getForm());
        if ($scale === null) {
            return;
        }

        $quantity = $data->getQuantity();
        $formattedQuantity = round($quantity, $scale, PHP_ROUND_HALF_UP);

        if ($quantity !== $formattedQuantity) {
            $data->setQuantity($formattedQuantity);
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return int|null
     */
    protected function getScale(FormInterface $form)
    {
        $productUnitField = 'unitDefinition';
        if (!$form->has($productUnitField)) {
            return null;
        }

        $productUnitDefinition = $form->get($productUnitField)->getData();
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('allow_units', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CartItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [CartItemType::class];
    }
}
