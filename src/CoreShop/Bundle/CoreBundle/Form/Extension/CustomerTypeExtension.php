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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\AddressBundle\Form\Type\SalutationChoiceType;
use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('salutation', SalutationChoiceType::class, [
                'label' => 'coreshop.form.customer.salutation',
            ]);

        if ($options['allow_default_address'] && $options['customer']) {
            $builder->add('defaultAddress', AddressChoiceType::class, [
                'customer' => $options['customer'],
                'label' => 'coreshop.form.customer.default_address',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('allow_default_address', false);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CustomerType::class];
    }
}
