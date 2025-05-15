<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use Doctrine\Common\Collections\Collection;
use Pimcore\Model\DataObject\ClassDefinition\Data\CustomDataCopyInterface;
use Pimcore\Model\DataObject\Concrete;

class ProductQuantityPriceRulesCloner implements ProductClonerInterface
{
    public function __construct(
        protected UnitMatcherInterface $unitMatcher,
    ) {
    }

    public function clone(
        ProductInterface $product,
        ProductInterface $referenceProduct,
        bool $resetExistingData = false,
    ): void {
        if ($product->getId() === null) {
            throw new \Exception(
                sprintf(
                    'cannot clone quantity price rules on a unsaved product (reference product id: %d.)',
                    $referenceProduct->getId(),
                ),
            );
        }

        $quantityPriceRules = $referenceProduct->getQuantityPriceRules();
        $hasQuantityPriceRules = count($product->getQuantityPriceRules()) > 0;

        if ($hasQuantityPriceRules === true && $resetExistingData === false) {
            return;
        }

        /**
         * @var Concrete&ProductInterface $referenceProduct
         *
         * @psalm-var Concrete&ProductInterface $referenceProduct
         */
        $qprFieldDefinition = $referenceProduct->getClass()->getFieldDefinition('quantityPriceRules');

        if (!$qprFieldDefinition instanceof CustomDataCopyInterface) {
            throw new \Exception('Field Definition must implement CustomDataCopyInterface');
        }

        $newQuantityPriceRules = $qprFieldDefinition->createDataCopy($referenceProduct, $quantityPriceRules);
        $this->reallocateRanges($product, $newQuantityPriceRules, $quantityPriceRules);

        $product->setQuantityPriceRules($newQuantityPriceRules);
    }

    protected function reallocateRanges(
        ProductInterface $product,
        array $newQuantityPriceRules,
        array $oldQuantityPriceRules,
    ) {
        if (count($oldQuantityPriceRules) !== count($newQuantityPriceRules)) {
            throw new \Exception('Count of old an new rules does not match');
        }

        foreach ($newQuantityPriceRules as $j => $newQuantityPriceRule) {
            $quantityPriceRule = $oldQuantityPriceRules[$j];

            $ranges = $newQuantityPriceRule->getRanges();
            $referenceRanges = $quantityPriceRule->getRanges();

            if (!$ranges instanceof Collection) {
                continue;
            }

            foreach ($ranges as $index => $range) {
                if (!$range instanceof QuantityRangeInterface) {
                    continue;
                }

                $referenceRange = $referenceRanges->get($index);

                if ($referenceRange instanceof QuantityRangeInterface &&
                    $referenceRange->getUnitDefinition() instanceof ProductUnitDefinitionInterface
                ) {
                    $allocatedUnitDefinition = $this->unitMatcher->findMatchingUnitDefinitionByUnitName(
                        $product,
                        $referenceRange->getUnitDefinition()->getUnitName(),
                    );

                    if (!$allocatedUnitDefinition instanceof ProductUnitDefinitionInterface) {
                        continue;
                    }

                    $range->setUnitDefinition($allocatedUnitDefinition);
                }
            }
        }
    }
}
