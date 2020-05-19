<?php


declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Form\Type;


use CoreShop\Bundle\ResourceBundle\Form\DataTransformer\PimcoreAssetDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PimcoreAssetChoiceType extends AbstractType
{
    private $transformer;

    public function __construct(PimcoreAssetDataTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'Pimcore Asset does not exist'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return IntegerType::class;
    }
}
