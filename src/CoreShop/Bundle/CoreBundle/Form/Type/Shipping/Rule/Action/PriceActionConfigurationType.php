<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Form\Type\Shipping\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PriceActionConfigurationType extends AbstractType
{
    /**
     * @var string[]
     */
    protected array $validationGroups = [];

    /**
     * @param string[] $validationGroups
     */
    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ]);

        $builder->get('currency')->addModelTransformer(new CallbackTransformer(
            function ($currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            },
            function ($currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            }
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_rule_action_price';
    }
}
