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
