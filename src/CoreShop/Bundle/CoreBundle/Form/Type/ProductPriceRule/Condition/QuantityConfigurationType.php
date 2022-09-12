<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\ProductPriceRule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class QuantityConfigurationType extends AbstractType
{
    /**
     * @param string[] $validationGroups
     */
    public function __construct(protected array $validationGroups)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minQuantity', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                ],
            ])
            ->add('maxQuantity', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_product_price_rule_condition_quantity';
    }
}
