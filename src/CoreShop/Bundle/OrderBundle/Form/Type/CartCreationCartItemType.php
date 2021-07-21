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

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartCreationCartItemType extends AbstractResourceType
{
    private DataMapperInterface $dataMapper;

    public function __construct(
        string $dataClass,
        array $validationGroups,
        DataMapperInterface $dataMapper
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->dataMapper = $dataMapper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['allow_product']) {
            $builder
                ->add('product', PurchasableSelectionType::class);
        }

        $builder
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'coreshop.ui.quantity',
            ])->setDataMapper($this->dataMapper);

        if ($options['allow_custom_price']) {
            $builder
                ->add('customItemDiscount', NumberType::class, [
                    'required' => false,
                    'data' => 0
                ])
                ->add('customItemPrice', IntegerType::class, [
                    'required' => false,
                    'data' => 0
                ])
            ;

            $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $e) {
                $data = $e->getData();

                if (!isset($data['customItemPrice'])) {
                    $data['customItemPrice'] = 0;
                }

                if (!isset($data['customItemDiscount'])) {
                    $data['customItemDiscount'] = 0;
                }

                $e->setData($data);
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('allow_product', true);
        $resolver->setDefault('allow_custom_price', true);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_cart_item';
    }
}
