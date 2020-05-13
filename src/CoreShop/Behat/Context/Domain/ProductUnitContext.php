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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductUnitContext implements Context
{
    private $sharedStorage;
    private $unitRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductUnitRepositoryInterface $unitRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->unitRepository = $unitRepository;
    }

    /**
     * @Then /^there should be a unit "([^"]+)"$/
     */
    public function thereShouldBeAUnit($name)
    {
        $unit = $this->unitRepository->findByName($name);

        Assert::notNull(
            $unit,
            sprintf('No unit has been found with name "%s".', $name)
        );
    }
}
