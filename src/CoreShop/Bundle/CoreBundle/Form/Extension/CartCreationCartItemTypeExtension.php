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

use CoreShop\Bundle\OrderBundle\Form\Type\CartCreationCartItemType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionsChoiceType;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CartCreationCartItemTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @param RepositoryInterface $productRepository
     */
    public function __construct(RepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $product = $data['product'];

            if (!isset($product)) {
                return;
            }

            $product = $this->productRepository->find($product);

            if (!$product instanceof ProductInterface) {
                return;
            }

            if (!$product->hasUnitDefinitions()) {
                return;
            }

            $event->getForm()->add('unitDefinition', ProductUnitDefinitionsChoiceType::class, [
                'product' => $product,
                'required' => false,
                'label' => null,
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CartCreationCartItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [CartCreationCartItemType::class];
    }
}
