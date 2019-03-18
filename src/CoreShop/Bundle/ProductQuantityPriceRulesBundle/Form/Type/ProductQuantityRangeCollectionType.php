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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type;

use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductQuantityRangeCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var ArrayCollection $data */
            $data = $event->getData();

            $lastEnd = -1;

            /**
             * @var int                    $rowIndex
             * @var QuantityRangeInterface $quantityRange
             */
            foreach ($data as $rowIndex => $quantityRange) {
                $realRowIndex = $rowIndex + 1;
                if (!is_numeric($quantityRange->getRangeFrom())) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . ' needs to be numeric'));

                    break;
                } elseif ((int) $quantityRange->getRangeFrom() < 0) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . '  needs to be greater or equal than 0'));

                    break;
                } elseif ((int) $quantityRange->getRangeFrom() <= $lastEnd) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . '  needs to be greater than ' . $lastEnd));

                    break;
                }

                if (!is_numeric($quantityRange->getRangeTo())) {
                    $event->getForm()->addError(new FormError('Field "to" in row ' . $realRowIndex . ' needs to be numeric'));

                    break;
                } elseif ((int) $quantityRange->getRangeTo() <= $quantityRange->getRangeFrom()) {
                    $event->getForm()->addError(new FormError('Field "to" in row ' . $realRowIndex . '  needs to be greater than ' . $quantityRange->getRangeFrom()));

                    break;
                }

                $lastEnd = (int) $quantityRange->getRangeTo();
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
            'entry_type' => ProductQuantityRangeType::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_quantity_price_rules_range_collection';
    }
}
