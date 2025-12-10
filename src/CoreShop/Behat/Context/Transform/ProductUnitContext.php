<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductUnitContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ProductUnitRepositoryInterface $unitRepository,
    ) {
    }

    /**
     * @Transform /^unit "([^"]+)"$/
     * @Transform /^product-unit "([^"]+)"$/
     */
    public function getUnitByName(string $name): ProductUnitInterface
    {
        $unit = $this->unitRepository->findByName($name);

        Assert::isInstanceOf($unit, ProductUnitInterface::class);

        return $unit;
    }

    /**
     * @Transform /^unit/
     * @Transform /^product-unit/
     */
    public function unit(): ProductUnitInterface
    {
        return $this->sharedStorage->get('product-unit');
    }
}
