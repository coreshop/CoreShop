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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductUnitContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductUnitRepositoryInterface
     */
    private $unitRepository;

    /**
     * @param SharedStorageInterface         $sharedStorage
     * @param ProductUnitRepositoryInterface $unitRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductUnitRepositoryInterface $unitRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->unitRepository = $unitRepository;
    }

    /**
     * @Transform /^unit "([^"]+)"$/
     * @Transform /^product-unit "([^"]+)"$/
     */
    public function getUnitByName($name)
    {
        $unit = $this->unitRepository->findByName($name);

        Assert::notNull($unit, sprintf('No unit with name %s found', $name));

        return $unit;
    }

    /**
     * @Transform /^unit/
     * @Transform /^product-unit/
     */
    public function unit()
    {
        return $this->sharedStorage->get('product-unit');
    }
}
