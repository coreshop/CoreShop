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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\ProductQuantityRangeCollectionType;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductQuantityRangeCollectionTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var ArrayCollection $data */
            $data = $event->getData();
            $form = $event->getForm();
            $dataCheck = [];

            /**
             * @var int                    $rowIndex
             * @var QuantityRangeInterface $quantityRange
             */
            foreach ($data as $rowIndex => $quantityRange) {
                $realRowIndex = $rowIndex + 1;

                $unit = $quantityRange->getUnitDefinition() instanceof ProductUnitDefinitionInterface ? $quantityRange->getUnitDefinition()->getUnitName() : 'default';

                if (!isset($dataCheck[$unit])) {
                    $dataCheck[$unit] = [];
                }

                $dataCheck[$unit][] = [
                    'row' => $realRowIndex,
                    'startingFrom' => $quantityRange->getRangeStartingFrom(),
                ];
            }

            foreach ($dataCheck as $unitName => $quantityRangesToCheck) {
                $lastEnd = -1;

                /**
                 * @var array $quantityRangeToCheck
                 */
                foreach ($quantityRangesToCheck as $quantityRangeToCheck) {
                    $realRowIndex = $quantityRangeToCheck['row'];
                    $startingFrom = $quantityRangeToCheck['startingFrom'];

                    if ((float) $startingFrom < 0) {
                        $form->addError(new FormError('Field "starting from" in row ' . $realRowIndex . '  needs to be greater or equal than 0'));

                        break;
                    }

                    if((float) $startingFrom <= $lastEnd) {
                        $form->addError(new FormError('Field "starting from" in row ' . $realRowIndex . '  needs to be greater than ' . $lastEnd));

                        break;
                    }

                    $lastEnd = (float) $startingFrom;
                }
            }
        });
    }


    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProductQuantityRangeCollectionType::class];
    }
}
