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

use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Repository\ProductUnitDefinitionRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductUnitDefinitionSelectionType extends AbstractType
{
    /**
     * @var ProductUnitDefinitionRepositoryInterface
     */
    protected $productUnitDefinitionRepository;

    /**
     * @param ProductUnitDefinitionRepositoryInterface $productUnitDefinitionRepository
     */
    public function __construct(ProductUnitDefinitionRepositoryInterface $productUnitDefinitionRepository)
    {
        $this->productUnitDefinitionRepository = $productUnitDefinitionRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if ($value instanceof ProductUnitDefinitionInterface) {
                    return $value->getId();
                }
                return null;
            },
            function ($value) {
                return $this->productUnitDefinitionRepository->find($value);
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'csrf_protection' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return NumberType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_unit_definition_selection';
    }
}
