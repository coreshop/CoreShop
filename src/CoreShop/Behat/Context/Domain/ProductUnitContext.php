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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Product\Repository\ProductUnitRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductUnitContext implements Context
{
    public function __construct(
        private ProductUnitRepositoryInterface $unitRepository,
    ) {
    }

    /**
     * @Then /^there should be a unit "([^"]+)"$/
     */
    public function thereShouldBeAUnit($name): void
    {
        $unit = $this->unitRepository->findByName($name);

        Assert::notNull(
            $unit,
            sprintf('No unit has been found with name "%s".', $name),
        );
    }
}
