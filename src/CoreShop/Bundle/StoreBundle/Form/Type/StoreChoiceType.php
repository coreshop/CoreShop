<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\StoreBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StoreChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $storeRepository;

    /**
     * @param RepositoryInterface $storeRepository
     */
    public function __construct(RepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function(Options $options) {
                    $stores = $this->storeRepository->findAll();

                    /*
                     * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
                     * "usort(): Array was modified by the user comparison function"
                     */
                    @usort($stores, function($a, $b) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    });

                    return $stores;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
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
        return 'coreshop_store_choice';
    }
}
