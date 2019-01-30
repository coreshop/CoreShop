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

namespace CoreShop\Bundle\TierPricingBundle\Form\Type;

use CoreShop\Component\TierPricing\Model\ProductTierPriceRangeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductSpecificTierPriceRangeCollectionType extends AbstractType
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
             * @var int                            $rowIndex
             * @var ProductTierPriceRangeInterface $tierPricesRange
             */
            foreach ($data as $rowIndex => $tierPricesRange) {
                $realRowIndex = $rowIndex + 1;

                if (!is_numeric($tierPricesRange->getRangeFrom())) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . ' needs to be numeric'));
                } elseif ((int) $tierPricesRange->getRangeFrom() < 0) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . '  needs to be greater or equal than 0'));
                } elseif ((int) $tierPricesRange->getRangeFrom() <= $lastEnd) {
                    $event->getForm()->addError(new FormError('Field "from" in row ' . $realRowIndex . '  needs to be greater than ' . $lastEnd));
                }

                if (!is_numeric($tierPricesRange->getRangeTo())) {
                    $event->getForm()->addError(new FormError('Field "to" in row ' . $realRowIndex . ' needs to be numeric'));
                } elseif ((int) $tierPricesRange->getRangeTo() <= $tierPricesRange->getRangeFrom()) {
                    $event->getForm()->addError(new FormError('Field "to" in row ' . $realRowIndex . '  needs to be greater than ' . $tierPricesRange->getRangeFrom()));
                }

                $lastEnd = (int) $tierPricesRange->getRangeTo();
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(
        OptionsResolver $resolver
    ) {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'error_bubbling' => false,
            'entry_type' => ProductSpecificTierPriceRangeType::class,
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
        return 'coreshop_product_specific_tier_price_range_collection';
    }
}
