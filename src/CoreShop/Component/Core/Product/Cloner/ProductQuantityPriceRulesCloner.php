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

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use Doctrine\Common\Collections\Collection;

class ProductQuantityPriceRulesCloner implements ProductClonerInterface
{
    public function clone(ProductInterface $product, ProductInterface $referenceProduct, bool $resetExistingData = false): void
    {
        if ($product->getId() === null) {
            throw new \Exception(sprintf('cannot clone quantity price rules on a un-stored product (reference product id: %d.', $referenceProduct->getId()));
        }

        $quantityPriceRules = $referenceProduct->getQuantityPriceRules();

        if (!is_array($quantityPriceRules)) {
            return;
        }

        $hasQuantityPriceRules = is_array($product->getQuantityPriceRules()) && count($product->getQuantityPriceRules()) > 0;

        if ($hasQuantityPriceRules === true && $resetExistingData === false) {
            return;
        }

        $newQuantityPriceRules = [];

        /** @var ProductQuantityPriceRuleInterface $quantityPriceRule */
        foreach ($quantityPriceRules as $quantityPriceRule) {
            $newQuantityPriceRules[] = $this->cloneAndReallocateRangeQuantityUnit($product, $quantityPriceRule);
        }

        if (count($newQuantityPriceRules) > 0) {
            $product->setQuantityPriceRules($newQuantityPriceRules);
        }
    }

    protected function cloneAndReallocateRangeQuantityUnit(ProductInterface $product, ProductQuantityPriceRuleInterface $quantityPriceRule): ProductQuantityPriceRuleInterface
    {
        $newQuantityPriceRule = clone $quantityPriceRule;

         //Hack to get rid of the ID
        $reflectionClass = new \ReflectionClass($newQuantityPriceRule);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($newQuantityPriceRule, null);

        foreach ([$newQuantityPriceRule->getConditions(), $newQuantityPriceRule->getActive()] as $batch) {
            foreach ($batch as $entry) {
                $reflectionClass = new \ReflectionClass($entry);
                $property = $reflectionClass->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($entry, null);
            }
        }

        $newQuantityPriceRule->setProduct($product->getId());

        $ranges = $newQuantityPriceRule->getRanges();
        $referenceRanges = $quantityPriceRule->getRanges();

        if (!$ranges instanceof Collection) {
            return $newQuantityPriceRule;
        }

        foreach ($ranges as $index => $range) {

            if (!$range instanceof QuantityRangeInterface) {
                continue;
            }

            $referenceRange = null;
            $allocatedUnitDefinition = null;

            if ($ranges instanceof Collection) {
                $referenceRange = $referenceRanges->get($index);
            }

            if ($referenceRange instanceof QuantityRangeInterface && $referenceRange->getUnitDefinition() instanceof ProductUnitDefinitionInterface) {
                $allocatedUnitDefinition = $this->findMatchingUnitDefinitionByUnitName($product, $referenceRange->getUnitDefinition()->getUnitName());
            }

            if (!$allocatedUnitDefinition instanceof ProductUnitDefinitionInterface) {
                continue;
            }

            $range->setUnitDefinition($allocatedUnitDefinition);

        }

        return $newQuantityPriceRule;
    }

    protected function findMatchingUnitDefinitionByUnitName(ProductInterface $product, string $unitName): ?ProductUnitDefinitionInterface
    {
        if ($product->hasUnitDefinitions() === false) {
            return null;
        }

        /** @var ProductUnitDefinitionInterface $unitDefinition */
        foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $unitDefinition) {

            if (!$unitDefinition instanceof ProductUnitDefinitionInterface) {
                continue;
            }

            if ($unitDefinition->getUnitName() === $unitName) {
                return $unitDefinition;
            }
        }

        return null;
    }
}
