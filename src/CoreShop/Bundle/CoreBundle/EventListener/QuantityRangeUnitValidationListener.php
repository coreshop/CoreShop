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

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Event\ProductQuantityPriceRuleValidationEvent;
use Pimcore\Model\Element\ValidationException;

final class QuantityRangeUnitValidationListener
{
    protected $productUnitDefinitionRepository;

    public function __construct(RepositoryInterface $productUnitDefinitionRepository)
    {
        $this->productUnitDefinitionRepository = $productUnitDefinitionRepository;
    }

    public function validate(ProductQuantityPriceRuleValidationEvent $event): void
    {
        $object = $event->getObject();
        $data = $event->getData();

        if (!$object instanceof ProductInterface) {
            return;
        }

        // this listener only validates variant data
        if ($object->getType() !== AbstractObject::OBJECT_TYPE_VARIANT) {
            return;
        }

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $rule) {
            $this->validateRule($rule, $object);
        }
    }

    protected function validateRule(array $rule, ProductInterface $product): void
    {
        if (!isset($rule['ranges']) || !is_array($rule['ranges'])) {
            return;
        }

        $ranges = $rule['ranges'];

        foreach ($ranges as $range) {

            if (!isset($range['unitDefinition']) || !is_int($range['unitDefinition'])) {
                continue;
            }

            $unitDefinitionId = $range['unitDefinition'];

            $unitDefinition = $this->productUnitDefinitionRepository->find($unitDefinitionId);
            if (!$unitDefinition instanceof ProductUnitDefinitionInterface) {
                continue;
            }

            $productUnitDefinitions = $unitDefinition->getProductUnitDefinitions();
            if (!$productUnitDefinitions instanceof ProductUnitDefinitionsInterface) {
                continue;
            }

            $unitDefinitionProduct = $productUnitDefinitions->getProduct();
            if (!$unitDefinitionProduct instanceof ProductInterface) {
                continue;
            }

            if ($unitDefinitionProduct->getId() !== $product->getId()) {
                throw new ValidationException('Invalid unit definition reference. Please reload the object and adjust the unit definitions first.');
            }
        }
    }
}
