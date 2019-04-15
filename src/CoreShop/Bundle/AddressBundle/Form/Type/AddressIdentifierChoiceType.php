<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\AddressBundle\Form\Type;

use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddressIdentifierChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $addressIdentifierRepository;

    /**
     * @param RepositoryInterface $addressIdentifierRepository
     */
    public function __construct(RepositoryInterface $addressIdentifierRepository)
    {
        $this->addressIdentifierRepository = $addressIdentifierRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    if (null === $options['active']) {
                        $addressIdentifier = $this->addressIdentifierRepository->findAll();
                    } else {
                        $addressIdentifier = $this->addressIdentifierRepository->findBy(['active' => $options['active']]);
                    }

                    usort($addressIdentifier, function (AddressIdentifierInterface $a, AddressIdentifierInterface $b): int {
                        return $a->getName() <=> $b->getName();
                    });

                    return $addressIdentifier;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'active' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_address_identifier_choice';
    }
}
