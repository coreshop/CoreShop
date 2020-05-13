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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\AddressBundle\Form\Type\CountryChoiceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('baseCountry', CountryChoiceType::class);
        $builder->add('useGrossPrice', CheckboxType::class);
        $builder->add('countries', CountryChoiceType::class, [
            'multiple' => true,
            'active' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return StoreType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): array
    {
        return [StoreType::class];
    }
}
