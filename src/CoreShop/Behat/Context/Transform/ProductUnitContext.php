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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ProductUnitInterface;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductUnitContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private ProductUnitRepositoryInterface $unitRepository)
    {
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
