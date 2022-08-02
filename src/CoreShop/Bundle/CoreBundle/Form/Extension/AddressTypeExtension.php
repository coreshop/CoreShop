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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddressTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $availableAffiliations = $options['available_affiliations'];

        if ($availableAffiliations === null) {
            return;
        }

        $builder->add('addressAffiliation', ChoiceType::class, [
            'mapped' => false,
            'label' => 'coreshop.form.customer.address_affiliation',
            'data' => $options['selected_affiliation'],
            'choices' => $availableAffiliations,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('available_affiliations', null);
        $resolver->setDefault('selected_affiliation', null);

        $resolver->setAllowedTypes('available_affiliations', ['null', 'array']);
        $resolver->setAllowedTypes('selected_affiliation', ['null', 'string']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddressType::class];
    }
}
