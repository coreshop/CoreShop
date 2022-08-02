<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class AdditionPercentActionConfigurationType extends AbstractType
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
            ->add('percent', NumberType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                    new Range(['min' => 0, 'max' => 100, 'groups' => $this->validationGroups]),
                ],
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_rule_action_addition_percent';
    }
}
