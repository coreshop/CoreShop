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

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreAssetChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PaymentProviderType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class)
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => PaymentProviderTranslationType::class,
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
            ])
            ->add('logo', PimcoreAssetChoiceType::class, [
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_payment_provider';
    }
}
