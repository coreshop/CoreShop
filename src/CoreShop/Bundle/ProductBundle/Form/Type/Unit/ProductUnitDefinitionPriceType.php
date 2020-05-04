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

declare(strict_types=1);

namespace CoreShop\Bundle\ProductBundle\Form\Type\Unit;

use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Product\Model\ProductUnitDefinitionPriceInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductUnitDefinitionPriceType extends AbstractResourceType
{
    protected $decimalFactor;
    protected $decimalPrecision;

    public function __construct(string $dataClass, array $validationGroups, int $decimalFactor, int $decimalPrecision)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class)
            ->add('unitDefinition', ProductUnitDefinitionSelectionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'coreshop_product_unit_definition_price';
    }
}
