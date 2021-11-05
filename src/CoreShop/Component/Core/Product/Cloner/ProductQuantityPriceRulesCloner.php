<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuantityRangeInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductQuantityPriceRulesCloner implements ProductClonerInterface
{
    /**
     * {@inheritDoc}
     */
    public function clone(ProductInterface $product, ProductInterface $referenceProduct, bool $resetExistingData = false)
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

    /**
     * @param ProductInterface                  $product
     * @param ProductQuantityPriceRuleInterface $quantityPriceRule
     *
     * @return ProductQuantityPriceRuleInterface
     * @throws \Exception
     */
    protected function cloneAndReallocateRangeQuantityUnit(ProductInterface $product, ProductQuantityPriceRuleInterface $quantityPriceRule)
    {
        $newQuantityPriceRule = clone $quantityPriceRule;

         //Hack to get rid of the ID
        $reflectionClass = new \ReflectionClass($newQuantityPriceRule);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($newQuantityPriceRule, null);

        $property = $reflectionClass->getProperty('product');
        $property->setAccessible(true);
        $property->setValue($newQuantityPriceRule, null);

        $property = $reflectionClass->getProperty('conditions');
        $property->setAccessible(true);
        $property->setValue($newQuantityPriceRule, new ArrayCollection());

        $property = $reflectionClass->getProperty('ranges');
        $property->setAccessible(true);
        $property->setValue($newQuantityPriceRule, new ArrayCollection());

        foreach ($quantityPriceRule->getConditions() as $condition) {
            $newCondition = clone $condition;

            $reflectionClass = new \ReflectionClass($newCondition);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($newCondition, null);

            $newQuantityPriceRule->addCondition($newCondition);
        }

        foreach ($quantityPriceRule->getRanges() as $range) {
            $newRange = clone $range;

            $reflectionClass = new \ReflectionClass($newRange);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($newRange, null);

            if ($reflectionClass->hasProperty('unitDefinition')) {
                $property = $reflectionClass->getProperty('unitDefinition');
                $property->setAccessible(true);
                $property->setValue($newRange, null);
            }

            $newQuantityPriceRule->addRange($newRange);
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

    /**
     * @param ProductInterface $product
     * @param string           $unitName
     *
     * @return ProductUnitDefinitionInterface|null
     */
    protected function findMatchingUnitDefinitionByUnitName(ProductInterface $product, string $unitName)
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
