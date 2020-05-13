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

namespace CoreShop\Bundle\AddressBundle\Form\Type;

use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SalutationChoiceType extends AbstractType
{
    private $countryContext;

    public function __construct(CountryContextInterface $countryContext)
    {
        $this->countryContext = $countryContext;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('country', $this->countryContext->getCountry())
            ->setAllowedValues('country', function ($country) {
                return $country instanceof CountryInterface;
            })
            ->setDefaults([
                'choices' => function (Options $options) {
                    $salutations = $options['country']->getSalutations();
                    $choices = [];
                    foreach ($salutations as $salutation) {
                        $translationKey = 'coreshop.form.customer.salutation.' . str_replace(' ', '_', strtolower(trim($salutation)));
                        $choices[$translationKey] = $salutation;
                    }

                    return $choices;
                },
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'coreshop_salutation_choice';
    }
}
