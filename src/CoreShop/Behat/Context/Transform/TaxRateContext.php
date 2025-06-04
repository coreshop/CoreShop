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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Taxation\Model\TaxRateInterface;
use CoreShop\Component\Taxation\Repository\TaxRateRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    public function __construct(
        private TaxRateRepositoryInterface $taxRateRepository,
    ) {
    }

    /**
     * @Transform /^tax rate "([^"]+)"$/
     */
    public function getTaxRateByName($name): TaxRateInterface
    {
        /**
         * @var TaxRateInterface[] $rates
         */
        $rates = $this->taxRateRepository->findByName($name, 'en');

        Assert::eq(
            count($rates),
            1,
            sprintf('%d country has been found with name "%s".', count($rates), $name),
        );

        return reset($rates);
    }
}
