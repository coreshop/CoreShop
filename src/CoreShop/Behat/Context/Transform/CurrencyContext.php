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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use Webmozart\Assert\Assert;

final class CurrencyContext implements Context
{
    private $sharedStorage;
    private $currencyRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @Transform /^currency "([^"]+)"$/
     */
    public function getCurrencyByIso($iso)
    {
        $currencies = $this->currencyRepository->findBy(['isoCode' => $iso]);

        Assert::eq(
            count($currencies),
            1,
            sprintf('%d currencies has been found with iso "%s".', count($currencies), $iso)
        );

        return reset($currencies);
    }

    /**
     * @Transform /^currency$/
     */
    public function latestCurrency()
    {
        return $this->sharedStorage->get('currency');
    }
}
