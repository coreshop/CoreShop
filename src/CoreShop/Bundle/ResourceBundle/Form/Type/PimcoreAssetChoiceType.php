<?php


namespace CoreShop\Bundle\ResourceBundle\Form\Type;


use CoreShop\Bundle\ResourceBundle\Form\DataTransformer\PimcoreAssetDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PimcoreAssetChoiceType extends AbstractType
{
    /**
     * @var PimcoreAssetDataTransformer
     */
    private $transformer;

    public function __construct(PimcoreAssetDataTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Pimcore Asset does not exist'
        ]);
    }

    public function getParent()
    {
        return IntegerType::class;
    }
}