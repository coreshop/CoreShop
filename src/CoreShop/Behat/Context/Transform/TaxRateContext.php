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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    private $taxRateRepository;

    public function __construct(TaxRateRepositoryInterface $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
    }

    /**
     * @Transform /^tax rate "([^"]+)"$/
     */
    public function getTaxRateByName($name)
    {
        $rates = $this->taxRateRepository->findByName($name, 'en');

        Assert::eq(
            count($rates),
            1,
            sprintf('%d country has been found with name "%s".', count($rates), $name)
        );

        return reset($rates);
    }
}
