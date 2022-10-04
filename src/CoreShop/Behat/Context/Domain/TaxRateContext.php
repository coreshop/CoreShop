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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    public function __construct(
        private TaxRateRepositoryInterface $taxRateRepository,
    ) {
    }

    /**
     * @Then /^there should be a tax rate "([^"]+)" with "([^"]+)%" rate$/
     */
    public function thereShouldBeATaxRate($name, $rate): void
    {
        $rates = $this->taxRateRepository->findByName($name, 'en');

        Assert::eq(
            count($rates),
            1,
            sprintf('%d tax-rate has been found with name "%s".', count($rates), $name),
        );

        $taxrate = reset($rates);

        Assert::eq(
            $taxrate->getRate(),
            $rate,
            sprintf('given rate "%d" is different from tax-rates rate "%s".', $rate, $taxrate->getRate()),
        );
    }
}
