<?php

namespace CoreShop\Bundle\IndexBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IndexChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $indexRepository;

    /**
     * @param RepositoryInterface $indexRepository
     */
    public function __construct(RepositoryInterface $indexRepository)
    {
        $this->indexRepository = $indexRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $indices = $this->indexRepository->findAll();

                    /*
                     * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
                     * "usort(): Array was modified by the user comparison function"
                     */
                    @usort($indices, function ($a, $b) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    });

                    return $indices;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ])
        ;
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
        return 'coreshop_index_choice';
    }
}
