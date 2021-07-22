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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private TaxRateRepositoryInterface $taxRateRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        TaxRateRepositoryInterface $taxRateRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxRateRepository = $taxRateRepository;
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
            sprintf('%d tax-rate has been found with name "%s".', count($rates), $name)
        );

        $taxrate = reset($rates);

        Assert::eq(
            $taxrate->getRate(),
            $rate,
            sprintf('given rate "%d" is different from tax-rates rate "%s".', $rate, $taxrate->getRate())
        );
    }
}
